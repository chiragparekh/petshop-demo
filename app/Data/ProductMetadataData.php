<?php

namespace App\Data;

use App\Casts\ProductMetadataCast;
use Illuminate\Contracts\Database\Eloquent\Castable;

class ProductMetadataData implements Castable
{
    public function __construct(
        public ?string $brand = null,
        public ?string $image = null
    ) {
    }

    public static function castUsing(array $arguments)
    {
        return ProductMetadataCast::class;
    }
}