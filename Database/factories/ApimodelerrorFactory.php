<?php

namespace Database\Factories;

use App\Models\Apimodelerror;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApimodelerrorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Apimodelerror::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'apimodel_id' => $this->faker->randomDigitNotNull,
        'smtp' => $this->faker->word,
        'error' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s'),
        'deleted_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
