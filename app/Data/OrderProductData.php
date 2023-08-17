<?php

namespace App\Data;

class OrderProductData
{
    public function __construct(
       public string $productUuid,
       public int $quantity = 1
    ) {}
}