<?php

namespace App\Casts;

use App\Data\OrderProductData;
use App\Data\OrderProductsData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class OrderProductsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $products = $attributes['products'] ? json_decode($attributes['products'], true) : [];

        $products = collect($products)->map(function(array $product) {
            return new OrderProductData(
                productUuid: $product['product'],
                quantity: $product['quantity']
            );
        });

        return new OrderProductsData($products->toArray());
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value instanceof OrderProductsData) {
            throw new InvalidArgumentException('The given value is not an OrderProductsData instance.');
        }

        /** @var OrderProductsData $value */
        $products = $value->map(function(OrderProductData $orderProduct) {
            return [
                'product' => $orderProduct->productUuid,
                'quantity' => $orderProduct->quantity
            ];
        })->toArray();

        return [
            'products' => json_encode($products),
        ];
    }
}
