<?php

declare(strict_types=1);

namespace App\Enums;

enum ReminderType: string
{
    case Payment = 'payment';
    case Pickup = 'pickup';
    case FollowUp = 'follow_up';
    case PrescriptionCheck = 'prescription_check';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Payment => 'Rappel de paiement / Relance solde',
            self::Pickup => 'Rappel de retrait / Commande prête',
            self::FollowUp => 'Suivi de satisfaction / Adaptation',
            self::PrescriptionCheck => 'Contrôle de la vue / Renouvellement annuel',
            self::Other => 'Autre rappel',
        };
    }
}
