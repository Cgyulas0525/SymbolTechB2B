<?php

namespace Database\Factories;

use App\Models\CustomerContactFavoriteProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerContactFavoriteProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerContactFavoriteProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customercontact_id' => $this->faker->randomDigitNotNull,
        'product_id' => $this->faker->randomDigitNotNull,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s'),
        'deleted_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
