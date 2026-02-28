<?php

namespace App\Enums;

enum OrderPaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Nieopłacone',
            self::Paid => 'Opłacone',
        };
    }
}
