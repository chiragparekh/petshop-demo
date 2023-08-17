<?php

namespace App\Data;

class OrderAddressData
{
    public function __construct(
        public string $billing = '',
        public string $shipping = '',
    )
    {}
}