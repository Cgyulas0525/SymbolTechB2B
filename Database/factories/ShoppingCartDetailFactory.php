<?php

namespace Database\Factories;

use App\Models\ShoppingCartDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingCartDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShoppingCartDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ShoppingCart' => $this->faker->word,
        'Currency' => $this->faker->word,
        'CurrencyRate' => $this->faker->word,
        'Product' => $this->faker->word,
        'Vat' => $this->faker->word,
        'QuantityUnit' => $this->faker->word,
        'Reverse' => $this->faker->word,
        'Quantity' => $this->faker->word,
        'CustomerOfferDetail' => $this->faker->word,
        'CustomerContractDetail' => $this->faker->word,
        'UnitPrice' => $this->faker->word,
        'DiscountPercent' => $this->faker->word,
        'DiscountUnitPrice' => $this->faker->word,
        'GrossPrices' => $this->faker->word,
        'DepositValue' => $this->faker->word,
        'DepositPercent' => $this->faker->word,
        'NetValue' => $this->faker->word,
        'GrossValue' => $this->faker->word,
        'VatValue' => $this->faker->word,
        'Comment' => $this->faker->word,
        'CustomerOrderDetail' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s'),
        'deleted_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
