<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorReadingController;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

// Add new sensor reading to table
Route::post('/iot/readings', [SensorReadingController::class, 'store']);

// Update dashboard
Route::get('/iot/readings', [SensorReadingController::class, 'index']);
Route::get('/iot/readings/latest', [SensorReadingController::class, 'latest']);