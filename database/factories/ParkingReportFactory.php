<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ParkingReport;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParkingReport>
 */
class ParkingReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'start_day' =>$this->faker->dateTimeThisMonth(),
            'end_day' =>$this->faker->dateTimeThisMonth(),
            'start_location' =>  $this->faker->text(10),
            'end_location' =>  $this->faker->text(10),    
        ];

       
    }
}
