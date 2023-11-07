<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TripplanReport;
use Illuminate\Support\Str;

// protected $Model = TripplanReport ::class;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripplanReport>
 */
class TripplanReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' =>$this->faker->randomDigit(),
            'vehicleid' =>$this->faker->randomDigit(),
            'vehicleid' =>$this->faker->randomDigit(),
            'vehicle_name' =>Str::slug($this->faker->text()),
            'start_location' =>  $this->faker->randomDigit(),
            'end_location' =>  $this->faker->randomDigit(),            
            'poc_number' => Str::slug($this->faker->text()),         
            'route_name' => Str::slug($this->faker->text()),   
            'geo_status' => $this->faker->randomDigit(),      
            'status' => $this->faker->randomDigit(),
        ];
    }
}
