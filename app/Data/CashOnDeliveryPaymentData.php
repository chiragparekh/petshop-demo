<?php

namespace App\Data;

class CashOnDeliveryPaymentData
{
    public function __construct(
       public string $firstName,
       public string $lastName,
       public string $address,
    ) {}
}