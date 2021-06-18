<?php

namespace Database\Factories;

use App\Models\FabricType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FabricTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FabricType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->numerify('Fabric type ###'),
            
        ];
    }
}
