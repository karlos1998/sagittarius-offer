<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingVerification = 'pending_verification';
    case Confirmed = 'confirmed';

    public function label(): string
    {
        return match ($this) {
            self::PendingVerification => 'Oczekuje na weryfikację',
            self::Confirmed => 'Potwierdzone',
        };
    }
}
