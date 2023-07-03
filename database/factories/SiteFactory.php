<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->company(),
            "plot_width"=>fake()->numberBetween(10,50),
            "plot_height"=>fake()->numberBetween(10,50),
            "plot_price"=>fake()->numberBetween(300000,3500000),
            "location"=>fake()->streetAddress(),
            "district"=>fake()->city(),
            "layout"=>"assets/files/area_44_layout.pdf",
        ];
    }
}
