<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorReading;
use Illuminate\Support\Facades\Validator;

class SensorReadingController extends Controller
{
    // Handle incoming sensor data
    public function store(Request $request)
    {
        // Device Authentication
        $expectedToken = (String) env('IOT_DEVICE_TOKEN', '');
        $providedToken = (String) $request->bearerToken();

        // Empty Token
        if($expectedToken === '')
        {
            return response()->json(['message' => 'Invalid token configuration'], 498);
        }

        // Token comparison not equal
        if(!hash_equals($expectedToken, $providedToken))
        {
            return response()->json(['message' => 'Unauthorized device'], 401);
        }

        // Validate HTTP Request incoming input field
        $validator = Validator::make(
            $request->all(),
            [
                'device_id' => 'required|string|max:50',
                'temperature' => 'required|numeric|between:-40,85',
                'humidity' => 'required|numeric|between:0,100',
                'recorded_at' => 'nullable|date'
            ]
        );

        // Invalid HTTP Request input field
        if($validator->fails())
        {
            return response()->json(
                [
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ],
                422
            );
        }

        // HTTP Request input field validated successfully; Add to table
        $reading = SensorReading::create(
            $validator->validated()
        );

        // Successful data acquisition
        return response()->json(
            [
                'message' => 'Sensor reading saved successfully',
                'data' => $reading
            ],
            201
        );
    }

    public function index()
    {
        return response->json(SensorReading::latest('id')->limit(20)->get());
    }
}
