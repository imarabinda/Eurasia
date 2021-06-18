<?php

namespace Database\Factories;

use App\Models\WeltedEdgesColor;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeltedEdgesColorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WeltedEdgesColor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->numerify('Welted Edges Color ###')
        ];
    }
}
