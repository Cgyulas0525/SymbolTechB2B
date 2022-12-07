<?php

namespace Database\Factories;

use App\Models\ShoppingCart;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingCartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShoppingCart::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'VoucherNumber' => $this->faker->word,
            'Customer' => $this->faker->word,
            'CustomerAddress' => $this->faker->word,
            'CustomerContact' => $this->faker->word,
            'VoucherDate' => $this->faker->date('Y-m-d H:i:s'),
            'DeliveryDate' => $this->faker->date('Y-m-d H:i:s'),
            'PaymentMethod' => $this->faker->word,
            'Currency' => $this->faker->word,
            'CurrencyRate' => $this->faker->word,
            'CustomerContract' => $this->faker->word,
            'TransportMode' => $this->faker->word,
            'DepositValue' => $this->faker->word,
            'DepositPercent' => $this->faker->word,
            'NetValue' => $this->faker->word,
            'GrossValue' => $this->faker->word,
            'VatValue' => $this->faker->word,
            'Comment' => $this->faker->word,
            'Opened' => $this->faker->word,
            'CustomerOrder' => $this->faker->word
        ];
    }
}
