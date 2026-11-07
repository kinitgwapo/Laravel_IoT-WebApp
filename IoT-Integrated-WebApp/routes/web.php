<?php

use App\Http\Controllers\Api\SensorReadingController;
use App\Models\SensorReading;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/iot-dashboard', [SensorReadingController::class, 'dashboard'])->name('iot.dashboard');
