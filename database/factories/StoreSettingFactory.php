<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreSetting>
 */
class StoreSettingFactory extends Factory
{
    protected $model = StoreSetting::class;

    public function definition(): array
    {
        return [
            'business_name' => 'ALBOPTIC',
            'trade_name' => 'ALBOPTIC Optique & Lunetterie',
            'address' => fake()->streetAddress(),
            'city' => 'Marrakech',
            'phone' => '+212 5 24 ' . fake()->numerify('## ## ##'),
            'email' => 'contact@alboptic.ma',
            'ice' => fake()->numerify('001234567890123'),
            'rc' => fake()->numerify('#####'),
            'tax_identifier' => fake()->numerify('######'),
            'logo_path' => null,
            'invoice_prefix' => 'FAC-',
            'order_prefix' => 'BC-',
            'default_tax_rate' => 20.00,
            'currency' => 'MAD',
        ];
    }
}
