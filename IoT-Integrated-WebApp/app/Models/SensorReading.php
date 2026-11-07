<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    // Attributes that can be massed assigned
    protected $fillable = [
        'device_id',
        'temperature',
        'humidity',
        'recorded_at'
    ];

    // How attributes are represented
    protected function casts(): array
    {
        return [
            'temperature' => 'float',
            'humidity' => 'float',
            'recorded_at' => 'datetime'
        ];
    }
}
