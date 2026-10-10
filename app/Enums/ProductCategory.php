<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductCategory: string
{
    case Frame = 'frame';
    case Lens = 'lens';
    case Sunglasses = 'sunglasses';
    case ContactLens = 'contact_lens';
    case Accessory = 'accessory';
    case Service = 'service';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Frame => 'Monture Optique',
            self::Lens => 'Verre / Lentille de vue',
            self::Sunglasses => 'Lunettes Solaires',
            self::ContactLens => 'Lentilles de contact',
            self::Accessory => 'Accessoire',
            self::Service => 'Prestation / Service atelier',
            self::Other => 'Autre',
        };
    }
}
