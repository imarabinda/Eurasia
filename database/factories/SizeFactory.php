<?php

namespace Database\Factories;

use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

class SizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Size::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'width'=>$this->faker->numberBetween($min = 14, $max = 50),
            'height'=>$this->faker->numberBetween($min = 14, $max = 50)
            
        ];
    }
}
