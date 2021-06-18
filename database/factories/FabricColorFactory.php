<?php

namespace Database\Factories;

use App\Models\FabricColor;
use Illuminate\Database\Eloquent\Factories\Factory;

class FabricColorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FabricColor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->numerify('Fabric color ###'),
        ];
    }
}
