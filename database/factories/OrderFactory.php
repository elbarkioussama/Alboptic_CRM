<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $orderDate = fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d');
        $subtotal = fake()->randomElement([800.00, 1200.00, 1600.00, 2400.00, 3200.00]);
        $discount = fake()->randomElement([0.00, 50.00, 100.00, 200.00]);
        $taxRate = 20.00;
        $taxable = max(0.0, $subtotal - $discount);
        $taxAmount = round($taxable * ($taxRate / 100.0), 2);
        $totalAmount = round($taxable + $taxAmount, 2);

        $amountPaid = fake()->randomElement([0.00, round($totalAmount / 2, 2), $totalAmount]);
        $remaining = round(max(0.0, $totalAmount - $amountPaid), 2);

        $paymentStatus = match (true) {
            $amountPaid <= 0.0 => PaymentStatus::Unpaid,
            $amountPaid >= $totalAmount => PaymentStatus::Paid,
            default => PaymentStatus::PartiallyPaid,
        };

        return [
            'client_id' => Client::factory(),
            'prescription_id' => null,
            'order_number' => 'BC-' . date('Y') . '-' . fake()->unique()->numerify('######'),
            'order_date' => $orderDate,
            'status' => fake()->randomElement([OrderStatus::Confirmed, OrderStatus::InProgress, OrderStatus::Ready, OrderStatus::Delivered]),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'remaining_amount' => $remaining,
            'payment_status' => $paymentStatus,
            'notes' => fake()->optional(0.3)->sentence(),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
