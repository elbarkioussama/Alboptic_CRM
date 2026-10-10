<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\OrderCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderCalculationService();
    }

    public function test_it_calculates_single_item_totals_correctly(): void
    {
        // 2 items @ 500 MAD each = 1000 MAD, 100 MAD discount = 900 MAD HT
        // 20% VAT = 180 MAD, Total TTC = 1080 MAD
        $totals = $this->service->calculateItemTotals(
            quantity: 2,
            unitPrice: 500.00,
            discount: 100.00,
            taxRate: 20.00
        );

        $this->assertEquals(900.00, $totals['subtotal']);
        $this->assertEquals(1080.00, $totals['total']);
    }

    public function test_it_determines_payment_status_correctly(): void
    {
        // Unpaid
        $this->assertEquals(
            PaymentStatus::Unpaid,
            $this->service->determinePaymentStatus(totalAmount: 1000.00, amountPaid: 0.00)
        );

        // Partially paid (advance)
        $this->assertEquals(
            PaymentStatus::PartiallyPaid,
            $this->service->determinePaymentStatus(totalAmount: 1000.00, amountPaid: 400.00)
        );

        // Fully paid
        $this->assertEquals(
            PaymentStatus::Paid,
            $this->service->determinePaymentStatus(totalAmount: 1000.00, amountPaid: 1000.00)
        );

        // Overpaid (handled as paid)
        $this->assertEquals(
            PaymentStatus::Paid,
            $this->service->determinePaymentStatus(totalAmount: 1000.00, amountPaid: 1200.00)
        );
    }

    public function test_it_recalculates_order_totals_and_balance_from_items_and_payments(): void
    {
        $client = Client::factory()->create();

        $order = Order::create([
            'client_id' => $client->id,
            'order_number' => 'BC-2026-TEST01',
            'order_date' => now()->toDateString(),
            'status' => OrderStatus::Confirmed,
            'subtotal' => 0.00,
            'discount' => 100.00, // 100 MAD global discount
            'tax_rate' => 20.00,
            'tax_amount' => 0.00,
            'total_amount' => 0.00,
            'amount_paid' => 0.00,
            'remaining_amount' => 0.00,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        // Item 1: Frame 1200 MAD HT
        OrderItem::create([
            'order_id' => $order->id,
            'item_type' => 'frame',
            'description' => 'Monture Test',
            'quantity' => 1,
            'unit_price' => 1200.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'subtotal' => 1200.00,
            'total' => 1440.00,
        ]);

        // Item 2: 2 Lenses @ 400 MAD = 800 MAD HT
        OrderItem::create([
            'order_id' => $order->id,
            'item_type' => 'lens',
            'description' => 'Verres Test',
            'quantity' => 2,
            'unit_price' => 400.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'subtotal' => 800.00,
            'total' => 960.00,
        ]);

        // Total HT items = 2000 MAD
        // Minus Order Discount 100 MAD = 1900 MAD Taxable HT
        // Tax 20% = 380 MAD
        // Total TTC = 2280 MAD

        // Payment 1: 500 MAD
        Payment::create([
            'order_id' => $order->id,
            'amount' => 500.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => PaymentMethod::Cash,
        ]);

        $updatedOrder = $this->service->recalculateOrder($order);

        $this->assertEquals(2000.00, (float) $updatedOrder->subtotal);
        $this->assertEquals(380.00, (float) $updatedOrder->tax_amount);
        $this->assertEquals(2280.00, (float) $updatedOrder->total_amount);
        $this->assertEquals(500.00, (float) $updatedOrder->amount_paid);
        $this->assertEquals(1780.00, (float) $updatedOrder->remaining_amount);
        $this->assertEquals(PaymentStatus::PartiallyPaid, $updatedOrder->payment_status);

        // Add second payment of 1780 MAD -> fully paid
        Payment::create([
            'order_id' => $order->id,
            'amount' => 1780.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => PaymentMethod::Card,
        ]);

        $finalOrder = $this->service->recalculateOrder($updatedOrder);

        $this->assertEquals(2280.00, (float) $finalOrder->amount_paid);
        $this->assertEquals(0.00, (float) $finalOrder->remaining_amount);
        $this->assertEquals(PaymentStatus::Paid, $finalOrder->payment_status);
    }
}
