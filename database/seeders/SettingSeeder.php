<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::Create([
            "name" => 'apemcl',
            "email" => 'apemcl@gmail.com',
            "address" => 'cbe',
            "logo" => 'apemc.jpg',
            "small_logo" => 'apemc.jpg',
            "mobile_number" => '1234567890'
        ]);
    }
}
