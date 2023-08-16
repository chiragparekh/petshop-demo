<?php

namespace Database\Factories;

use App\Data\BankTransferPaymentData;
use App\Data\CashOnDeliveryPaymentData;
use App\Data\CreditCardPaymentData;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(PaymentType::values());

        return [
            'type' => $type,
            'details' => $this->getPaymentDetails($type)
        ];
    }

    private function getPaymentDetails(string $type): CreditCardPaymentData | CashOnDeliveryPaymentData | BankTransferPaymentData
    {
        return match($type) {
            PaymentType::CREDIT_CARD->value => new CreditCardPaymentData(
                holderName: $this->faker->name(),
                number: $this->faker->creditCardNumber(),
                ccv: 123,
                expireDate: $this->faker->creditCardExpirationDateString(),
            ),
            PaymentType::CASH_ON_DELIVERY->value => new CashOnDeliveryPaymentData(
                firstName: $this->faker->firstName(),
                lastName: $this->faker->lastName(),
                address: $this->faker->address(),
            ),
            PaymentType::BANK_TRANSFER->value => new BankTransferPaymentData(
                swift: $this->faker->swiftBicNumber(),
                iban: $this->faker->iban(),
                name: $this->faker->name(),
            )
        };
    }
}
