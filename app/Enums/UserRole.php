<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Optician = 'optician';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Optician => 'Opticien',
            self::Staff => 'Personnel',
        };
    }
}
