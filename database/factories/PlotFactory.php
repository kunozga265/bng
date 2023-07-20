<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plot>
 */
class PlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name"=> fake()->city(),
            "status"=>1,
            "hectare"=>fake()->randomFloat(5,0.5,3),
            "site_id"=>fake()->numberBetween(1,5),
//            "user_id"=>"",
        ];
    }
}
