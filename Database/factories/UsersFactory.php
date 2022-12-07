<?php

namespace Database\Factories;

use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsersFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Users::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        'email' => $this->faker->word,
        'email_verified_at' => $this->faker->date('Y-m-d H:i:s'),
        'password' => $this->faker->word,
        'employee_id' => $this->faker->randomDigitNotNull,
        'customercontact_id' => $this->faker->randomDigitNotNull,
        'rendszergazda' => $this->faker->randomDigitNotNull,
        'megjegyzes' => $this->faker->text,
        'CustomerAddress' => $this->faker->word,
        'TransportMode' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s'),
        'deleted_at' => $this->faker->date('Y-m-d H:i:s'),
        'remember_token' => $this->faker->word,
        'image_url' => $this->faker->word
        ];
    }
}
