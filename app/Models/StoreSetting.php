<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $table = 'store_settings';

    protected $fillable = [
        'business_name',
        'trade_name',
        'address',
        'city',
        'phone',
        'email',
        'ice',
        'rc',
        'tax_identifier',
        'logo_path',
        'invoice_prefix',
        'order_prefix',
        'default_tax_rate',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'default_tax_rate' => 'decimal:2',
        ];
    }

    /**
     * Retrieve the active store settings or create default instance.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'business_name' => 'ALBOPTIC',
                'trade_name' => 'ALBOPTIC Optique & Lunetterie',
                'address' => 'Gueliz, Marrakech',
                'city' => 'Marrakech',
                'phone' => '+212 5 24 00 00 00',
                'email' => 'contact@alboptic.ma',
                'ice' => '000000000000000',
                'rc' => '12345',
                'tax_identifier' => '67890',
                'invoice_prefix' => 'FAC-',
                'order_prefix' => 'BC-',
                'default_tax_rate' => 20.00,
                'currency' => 'MAD',
            ]
        );
    }
}
