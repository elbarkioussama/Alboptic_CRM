<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemType: string
{
    case Frame = 'frame';
    case Lens = 'lens';
    case Sunglasses = 'sunglasses';
    case Accessory = 'accessory';
    case Service = 'service';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Frame => 'Monture',
            self::Lens => 'Verre',
            self::Sunglasses => 'Solaire',
            self::Accessory => 'Accessoire',
            self::Service => 'Service',
            self::Other => 'Autre',
        };
    }
}
