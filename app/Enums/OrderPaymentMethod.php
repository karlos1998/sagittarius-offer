<?php

namespace App\Enums;

enum OrderPaymentMethod: string
{
    case PayOnSite = 'pay_on_site';

    public function label(): string
    {
        return match ($this) {
            self::PayOnSite => 'Zapłać na miejscu',
        };
    }
}
