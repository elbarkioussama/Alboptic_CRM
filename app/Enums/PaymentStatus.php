<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Impayé',
            self::PartiallyPaid => 'Avance / Partiel',
            self::Paid => 'Réglé intégralement',
            self::Refunded => 'Remboursé',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-red-50 text-red-700 border-red-200',
            self::PartiallyPaid => 'bg-amber-50 text-amber-800 border-amber-200',
            self::Paid => 'bg-teal-50 text-teal-800 border-teal-200',
            self::Refunded => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
