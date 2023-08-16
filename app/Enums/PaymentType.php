<?php

namespace App\Enums;

enum PaymentType: string
{
    case CREDIT_CARD = 'credit_card';
    case CASH_ON_DELIVERY = 'cash_on_delivery';
    case BANK_TRANSFER = 'bank_transfer';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
