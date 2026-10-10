<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReminderStatus;
use App\Enums\ReminderType;
use App\Models\Client;
use App\Models\Order;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'order_id' => null,
            'reminder_type' => fake()->randomElement(ReminderType::cases()),
            'reminder_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'status' => fake()->randomElement(ReminderStatus::cases()),
            'notes' => fake()->optional(0.4)->sentence(),
        ];
    }
}
