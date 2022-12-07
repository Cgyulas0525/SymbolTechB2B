<?php

namespace Database\Factories;

use App\Models\Apimodel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApimodelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Apimodel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'api_id' => $this->faker->randomDigitNotNull,
        'model' => $this->faker->word,
        'recordnumber' => $this->faker->randomDigitNotNull,
        'insertednumber' => $this->faker->randomDigitNotNull,
        'updatednumber' => $this->faker->randomDigitNotNull,
        'errornumber' => $this->faker->randomDigitNotNull,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s'),
        'deleted_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
