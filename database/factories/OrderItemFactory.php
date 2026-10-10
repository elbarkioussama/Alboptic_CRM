<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ItemType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $unitPrice = fake()->randomElement([250.00, 400.00, 600.00, 950.00]);
        $quantity = fake()->numberBetween(1, 2);
        $discount = 0.00;
        $taxRate = 20.00;
        $subtotal = round(($unitPrice * $quantity) - $discount, 2);
        $taxAmount = round($subtotal * ($taxRate / 100.0), 2);
        $total = round($subtotal + $taxAmount, 2);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'item_type' => fake()->randomElement(ItemType::cases()),
            'description' => fake()->randomElement(['Monture Optique Acétate', 'Paire de verres Anti-reflet', 'Nettoyage & Ajustage']),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'tax_rate' => $taxRate,
            'subtotal' => $subtotal,
            'total' => $total,
            'metadata' => [
                'lens_index' => '1.60',
                'coating' => 'Anti-reflet + Blue block',
            ],
        ];
    }
}
