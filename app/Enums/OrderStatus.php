<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case InProgress = 'in_progress';
    case Ready = 'ready';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Confirmed => 'Confirmée',
            self::InProgress => 'En cours d\'atelier',
            self::Ready => 'Prête',
            self::Delivered => 'Livrée',
            self::Cancelled => 'Annulée',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700 border-slate-200',
            self::Confirmed => 'bg-blue-50 text-blue-700 border-blue-200',
            self::InProgress => 'bg-sky-50 text-sky-700 border-sky-200',
            self::Ready => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Delivered => 'bg-teal-50 text-teal-700 border-teal-200',
            self::Cancelled => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
