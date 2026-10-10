<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->randomElement([200.00, 300.00, 500.00, 1000.00]),
            'payment_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'reference' => fake()->optional(0.5)->bothify('PAY-######'),
            'notes' => fake()->optional(0.2)->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
