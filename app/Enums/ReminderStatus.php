<?php

declare(strict_types=1);

namespace App\Enums;

enum ReminderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Completed => 'Effectué',
            self::Cancelled => 'Annulé',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Completed => 'emerald',
            self::Cancelled => 'gray',
        };
    }
}
