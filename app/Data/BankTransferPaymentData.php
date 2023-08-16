<?php

namespace App\Data;

class BankTransferPaymentData
{
    public function __construct(
       public string $swift,
       public string $iban,
       public string $name,
    ) {}
}