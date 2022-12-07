<?php

namespace Database\Factories;

use App\Models\LogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LogItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => $this->faker->randomDigitNotNull,
        'user_id' => $this->faker->randomDigitNotNull,
        'eventtype' => $this->faker->randomDigitNotNull,
        'eventdatetime' => $this->faker->date('Y-m-d H:i:s'),
        'remoteaddress' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s'),
        'deleted_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
