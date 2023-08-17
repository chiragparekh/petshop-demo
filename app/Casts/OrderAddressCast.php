<?php

namespace App\Casts;

use App\Data\OrderAddressData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class OrderAddressCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $address = $value ? json_decode($value, true) : null;

        return new OrderAddressData(
            billing: $address['billing'] ?? '',
            shipping: $address['shipping'] ?? '',
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value instanceof OrderAddressData) {
            throw new InvalidArgumentException('The given value is not an OrderAddressData instance.');
        }

        /** @var OrderAddressData $value */
        return [
            'address' => json_encode([
                'billing' => $value->billing,
                'shipping' => $value->shipping,
            ])
        ];
    }
}
