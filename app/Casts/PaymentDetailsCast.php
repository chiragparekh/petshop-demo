<?php

namespace App\Casts;

use App\Data\BankTransferPaymentData;
use App\Data\CashOnDeliveryPaymentData;
use App\Data\CreditCardPaymentData;
use App\Enums\PaymentType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class PaymentDetailsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $details = $attributes['details'] ? json_decode($attributes['details'], true) : null;

        $result = null;

        switch ($attributes['type']) {
            case PaymentType::CREDIT_CARD->value:
                $result = new CreditCardPaymentData(
                    holderName: $details['holder_name'],
                    number: $details['number'],
                    ccv: $details['ccv'],
                    expireDate: $details['expire_date']
                );
                break;
            case PaymentType::CASH_ON_DELIVERY->value:
                $result = new CashOnDeliveryPaymentData(
                    firstName: $details['first_name'],
                    lastName: $details['last_name'],
                    address: $details['address'],
                );
                break;
            case PaymentType::BANK_TRANSFER->value:
                $result = new BankTransferPaymentData(
                    swift: $details['swift'],
                    iban: $details['iban'],
                    name: $details['name']
                );
                break;
        }

        return $result;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value instanceof CreditCardPaymentData
            && ! $value instanceof CashOnDeliveryPaymentData
            && ! $value instanceof BankTransferPaymentData
        ) {
            throw new InvalidArgumentException('The given value is not an ProductMetadataData instance.');
        }

        $data = [];

        switch ($attributes['type']) {
            case PaymentType::CREDIT_CARD->value:
                /** @var CreditCardPaymentData $value */
                $data = [
                    'details' => json_encode([
                        'holder_name' => $value->holderName,
                        'number' => $value->number,
                        'ccv' => $value->ccv,
                        'expire_date' => $value->expireDate,
                    ]),
                ];
                break;

            case PaymentType::CASH_ON_DELIVERY->value:
                /** @var CashOnDeliveryPaymentData $value */
                $data = [
                    'details' => json_encode([
                        'first_name' => $value->firstName,
                        'last_name' => $value->lastName,
                        'address' => $value->address,
                    ]),
                ];
                break;
            case PaymentType::BANK_TRANSFER->value:
                /** @var BankTransferPaymentData $value */
                $data = [
                    'details' => json_encode([
                        'swift' => $value->swift,
                        'iban' => $value->iban,
                        'name' => $value->name,
                    ]),
                ];
                break;

        }

        return $data;
    }
}
