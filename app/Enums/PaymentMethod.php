<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::Card => 'Carte bancaire / TPE',
            self::BankTransfer => 'Virement bancaire',
            self::Check => 'Chèque',
            self::Other => 'Autre',
        };
    }
}
