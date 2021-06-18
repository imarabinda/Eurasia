<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text($maxNbChars = 200),
            'image'=>$this->faker->imageUrl($width = 640, $height = 480),
            'name'=>$this->faker->numerify('Product category ###'),
            'code' => $this->faker->randomNumber($nbDigits = NULL, $strict = false),
            'price'=>$this->faker->numberBetween($min=100,$max=2000),
            'qty'=>$this->faker->numberBetween($min=0,$max=50),
        ];
    }
}
