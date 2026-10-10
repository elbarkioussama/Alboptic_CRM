<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'description',
        'brand',
        'supplier',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => ProductCategory::class,
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, ProductCategory|string $category): Builder
    {
        $val = $category instanceof ProductCategory ? $category->value : $category;
        return $query->where('category', $val);
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
