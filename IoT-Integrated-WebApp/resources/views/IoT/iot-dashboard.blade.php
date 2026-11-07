<!DOCTYPE html>
<html>
<head>
    <meta name="author" content="Kenneth O. Restauro">
    <meta name="description" content="IoT-Dashboard Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IoT-Dashboard Page</title>

    <!-- Injects adjacent edit.css file directly -->
    <style>
        {!! file_get_contents(resource_path('views/IoT/Styles/iot-dashboard.css')) !!}
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Smart Greenhouse Monitoring</h1>
            <p class="muted">ESP32 + Laravel + MySQL IoT System</p>
            <button class="refresh" onclick="location.reload()">
                Refresh Readings
            </button>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Temperature</h3>
                <div class="value">
                    {{ $latest ? number_format($latest->temperature, 2) . ' °C' : '--' }}
                </div>
                <p class="muted">Latest sensor reading</p>
            </div>

            <div class="card">
                <h3>Humidity</h3>
                <div class="value">
                    {{ $latest ? number_format($latest->humidity, 2) . ' %' : '--' }}
                </div>
                <p class="muted">Relative humidity</p>
            </div>

            <div class="card">
                <h3>Device ID</h3>
                <div class="value" style="font-size: 20px">
                    {{ $latest?->device_id ?? 'No device' }}
                </div>
                <p class="muted">Most recent reporting device</p>
            </div>

            <div class="card">
                <h3>Last Received</h3>
                <div style="font-size: 18px; font-weight: bold">
                    {{ $latest ? $latest->created_at->format('M d, Y h:i:s A') : 'No readings yet' }}
                </div>
                <p class="muted">Laravel server time</p>
            </div>
        </div>

        <div class="panel">
            <h2>Sensor Reading History</h2>

            @if ($readings->isEmpty())
                <p>No sensor readings available yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th><th>Device</th><th>Temperature</th>
                            <th>Humidity</th><th>Recorded At</th><th>Received At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($readings as $reading)
                            <tr>
                                <td>{{ $reading->id }}</td>
                                <td>{{ $reading->device_id }}</td>
                                <td>{{ number_format($reading->temperature, 2) }} °C</td>
                                <td>{{ number_format($reading->humidity, 2) }} %</td>
                                <td>
                                    {{ $reading->recorded_at
                                        ? $reading->recorded_at->format('M d, Y h:i:s A')
                                        : 'Not provided' }}
                                </td>
                                <td>{{ $reading->created_at->format('M d, Y h:i:s A') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>