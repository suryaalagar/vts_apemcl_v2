<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Vehicle extends Model
{
    use HasFactory;


    public function rules()
    {
        return [
            'title' => [
                'required',
                Rule::unique('device_imei', 'sim_mob_no')->ignore($this->post)
            ]
        ];
    }
}
