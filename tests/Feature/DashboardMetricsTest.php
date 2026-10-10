<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReminderStatus;
use App\Enums\ReminderType;
use App\Livewire\Dashboard\DashboardOverview;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reminder;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_computes_exact_sales_and_payments_from_database(): void
    {
        $user = User::factory()->create();
        StoreSetting::current();

        $client = Client::factory()->create();

        // 1. Order today: 1500 MAD
        $orderToday = Order::create([
            'client_id' => $client->id,
            'order_number' => 'BC-2026-TODAY1',
            'order_date' => now()->toDateString(),
            'status' => OrderStatus::Confirmed,
            'subtotal' => 1250.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'tax_amount' => 250.00,
            'total_amount' => 1500.00,
            'amount_paid' => 500.00,
            'remaining_amount' => 1000.00,
            'payment_status' => PaymentStatus::PartiallyPaid,
        ]);

        // 2. Order past: 2000 MAD with 800 remaining
        $orderPast = Order::create([
            'client_id' => $client->id,
            'order_number' => 'BC-2026-PAST1',
            'order_date' => now()->subDays(10)->toDateString(),
            'status' => OrderStatus::Delivered,
            'subtotal' => 2000.00,
            'total_amount' => 2400.00,
            'amount_paid' => 1600.00,
            'remaining_amount' => 800.00,
            'payment_status' => PaymentStatus::PartiallyPaid,
        ]);

        // 3. Payments: 500 today, 1000 today on another order, 1600 in the past
        Payment::create([
            'order_id' => $orderToday->id,
            'amount' => 500.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => PaymentMethod::Cash,
        ]);

        Payment::create([
            'order_id' => $orderPast->id,
            'amount' => 1000.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => PaymentMethod::Card,
        ]);

        Payment::create([
            'order_id' => $orderPast->id,
            'amount' => 600.00,
            'payment_date' => now()->subDays(10)->toDateString(),
            'payment_method' => PaymentMethod::Card,
        ]);

        // 4. Pending reminder
        Reminder::create([
            'client_id' => $client->id,
            'order_id' => $orderToday->id,
            'reminder_type' => ReminderType::Pickup,
            'reminder_date' => now()->toDateString(),
            'status' => ReminderStatus::Pending,
            'notes' => 'Lunettes prêtes au retrait',
        ]);

        // Expected metrics:
        // Today's Sales = 1500.00 MAD
        // Today's Payments = 500 + 1000 = 1500.00 MAD
        // Total Outstanding = 1000 + 800 = 1800.00 MAD
        // Today's Orders = 1

        Livewire::actingAs($user)
            ->test(DashboardOverview::class)
            ->assertViewHas('todaySales', 1500.00)
            ->assertViewHas('todayPayments', 1500.00)
            ->assertViewHas('outstandingPayments', 1800.00)
            ->assertViewHas('todayOrdersCount', 1)
            ->assertViewHas('totalClientsCount', 1)
            ->assertSee('BC-2026-TODAY1')
            ->assertSee('Lunettes prêtes au retrait');
    }
}
