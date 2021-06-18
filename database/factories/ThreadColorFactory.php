<?php

namespace Database\Factories;

use App\Models\ThreadColor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadColorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ThreadColor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->numerify('Thread Color ###')
        ];
    }
}
