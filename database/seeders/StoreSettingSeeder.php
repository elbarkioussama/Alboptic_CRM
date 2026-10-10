<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        StoreSetting::updateOrCreate(
            ['id' => 1],
            [
                'business_name' => 'ALBOPTIC',
                'trade_name' => 'ALBOPTIC Optique & Lunetterie',
                'address' => 'Avenue Mohammed V, Guéliz',
                'city' => 'Marrakech',
                'phone' => '+212 5 24 43 20 10',
                'email' => 'contact@alboptic.ma',
                'ice' => '001928374650001',
                'rc' => '48291',
                'tax_identifier' => '39201948',
                'logo_path' => null,
                'invoice_prefix' => 'FAC-',
                'order_prefix' => 'BC-',
                'default_tax_rate' => 20.00,
                'currency' => 'MAD',
            ]
        );
    }
}
