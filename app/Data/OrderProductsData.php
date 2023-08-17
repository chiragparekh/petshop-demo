<?php

namespace App\Data;

use Illuminate\Support\Collection;

class OrderProductsData extends Collection
{
    public function __construct(array $orderProducts)
    {
        parent::__construct();

        /** @var OrderProductData $orderProduct */
        foreach($orderProducts as $orderProduct) {
            $this->push($orderProduct);
        }
    }
}