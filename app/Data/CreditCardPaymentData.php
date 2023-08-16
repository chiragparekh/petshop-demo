<?php

namespace App\Data;

class CreditCardPaymentData
{
    public function __construct(
        public string $holderName,
        public string $number,
        public int $ccv,
        public string $expireDate
    )
    {}
}