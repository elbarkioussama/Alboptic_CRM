<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentType: string
{
    case BonCommande = 'bon_commande';
    case Facture = 'facture';

    public function label(): string
    {
        return match ($this) {
            self::BonCommande => 'Bon de Commande',
            self::Facture => 'Facture',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::BonCommande => 'BC-',
            self::Facture => 'FAC-',
        };
    }
}
