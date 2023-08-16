<?php

namespace App\Casts;

use App\Data\ProductMetadataData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ProductMetadataCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $metaData = $attributes['metadata'] ? json_decode($attributes['metadata'], true) : null;

        if(! $metaData) {
            return new ProductMetadataData();
        }

        return new ProductMetadataData(
            brand: $metaData['brand'],
            image: $metaData['image']
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value instanceof ProductMetadataData) {
            throw new InvalidArgumentException('The given value is not an ProductMetadataData instance.');
        }

        return [
            'metadata' => json_encode([
                'image' => $value?->image,
                'brand' => $value?->brand,
            ]),
        ];
    }
}
