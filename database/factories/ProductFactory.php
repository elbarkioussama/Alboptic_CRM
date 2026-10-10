<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProductCategory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $category = fake()->randomElement(ProductCategory::cases());
        $sellingPrice = match ($category) {
            ProductCategory::Frame => fake()->randomElement([450.00, 600.00, 850.00, 1200.00, 1800.00, 2500.00]),
            ProductCategory::Lens => fake()->randomElement([300.00, 450.00, 650.00, 900.00, 1400.00]),
            ProductCategory::Sunglasses => fake()->randomElement([500.00, 750.00, 1100.00, 1900.00]),
            ProductCategory::Accessory => fake()->randomElement([50.00, 80.00, 120.00, 150.00]),
            ProductCategory::Service => fake()->randomElement([50.00, 100.00, 150.00]),
            default => 200.00,
        };

        $costPrice = round($sellingPrice * 0.45, 2);

        return [
            'sku' => strtoupper(fake()->unique()->bothify('OPT-####-???')),
            'name' => fake()->randomElement(['Monture Titanium Flex', 'Verre Anti-Reflet Premium', 'Lunette Solaire Polarized', 'Étui Rigide Protection', 'Spray Nettoyant Microfibre', 'Montage & Calibrage']),
            'category' => $category,
            'description' => fake()->optional(0.6)->sentence(),
            'brand' => fake()->randomElement(['Ray-Ban', 'Gucci', 'Essilor', 'Hoya', 'Vogue', 'Oakley', 'Persol']),
            'supplier' => fake()->randomElement(['OptiMorocco SARL', 'Vision Distribution Maghreb', 'Atlas Optique']),
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock_quantity' => fake()->numberBetween(2, 25),
            'is_active' => true,
        ];
    }
}
