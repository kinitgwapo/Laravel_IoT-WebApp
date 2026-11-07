<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="author" content="Kenneth O. Restauro">
    <meta name="description" content="IoT-Dashboard Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Smart Greenhouse | Upside Down Monitoring</title>

    <style>
        {!! file_get_contents(resource_path('views/IoT/Styles/iot-dashboard.css')) !!}
    </style>
</head>

<body>

    <!-- Atmospheric Background -->
    <div class="mind-flayer"></div>
    <div class="spores"></div>

    <main class="dashboard">

        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-brand">
                <div class="brand-icon">SG</div>

                <div>
                    <h1>SMART GREENHOUSE</h1>
                    <p class="badge-subtitle">IoT MONITORING SYSTEM</p>
                </div>
            </div>

            <div class="header-actions">
                <div class="system-status">
                    <span class="status-indicator"></span>
                    SYSTEM MONITORING
                </div>

                <button class="refresh" onclick="location.reload()">
                    ↻ REFRESH READINGS
                </button>
            </div>
        </header>


        <!-- Monitoring Overview -->
        <section class="overview">

            <div class="section-heading">
                <h2>ENVIRONMENTAL READINGS</h2>
                <p>LIVE SENSOR MONITORING</p>
            </div>

            <div class="metrics-grid">

                <!-- Temperature -->
                <article class="metric-card temperature-card">
                    <div class="metric-top">
                        <span class="metric-label">TEMPERATURE</span>
                        <span class="metric-icon">°C</span>
                    </div>

                    <div class="metric-value">
                        {{ $latest ? number_format($latest->temperature, 2) : '--' }}
                        <span>°C</span>
                    </div>

                    <p class="metric-description">
                        Latest sensor measurement
                    </p>
                </article>


                <!-- Humidity -->
                <article class="metric-card humidity-card">
                    <div class="metric-top">
                        <span class="metric-label">HUMIDITY</span>
                        <span class="metric-icon">%</span>
                    </div>

                    <div class="metric-value">
                        {{ $latest ? number_format($latest->humidity, 2) : '--' }}
                        <span>%</span>
                    </div>

                    <p class="metric-description">
                        Relative humidity
                    </p>
                </article>


                <!-- Device -->
                <article class="metric-card device-card">
                    <div class="metric-top">
                        <span class="metric-label">REPORTING DEVICE</span>
                        <span class="metric-icon">ID</span>
                    </div>

                    <div class="metric-value device-value">
                        {{ $latest?->device_id ?? 'NO DEVICE' }}
                    </div>

                    <p class="metric-description">
                        Most recent reporting device
                    </p>
                </article>


                <!-- Last Received -->
                <article class="metric-card received-card">
                    <div class="metric-top">
                        <span class="metric-label">LAST RECEIVED</span>
                        <span class="metric-icon">◷</span>
                    </div>

                    <div class="metric-value time-value">
                        {{ $latest ? $latest->created_at->format('h:i:s A') : '--:--:--' }}
                    </div>

                    <p class="metric-description">
                        {{ $latest ? $latest->created_at->format('M d, Y') : 'NO READINGS YET' }}
                    </p>
                </article>

            </div>
        </section>


        <!-- Sensor History -->
        <section class="history-panel">

            <div class="panel-header">
                <div>
                    <h2>TRANSMISSION HISTORY</h2>
                    <p>RECORDED SENSOR DATA AND SERVER RECEIPTS</p>
                </div>

                <span class="record-count">
                    {{ $readings->count() }} RECORDS
                </span>
            </div>

            @if ($readings->isEmpty())

                <div class="empty-state">
                    <div class="empty-icon">⌁</div>
                    <h3>NO TRANSMISSIONS DETECTED</h3>
                    <p>Waiting for the first sensor transmission...</p>
                </div>

            @else

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Device ID</th>
                                <th>Temperature</th>
                                <th>Humidity</th>
                                <th>Recorded At</th>
                                <th>Received At</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($readings as $reading)
                                <tr>
                                    <td class="record-id">
                                        #{{ $reading->id }}
                                    </td>

                                    <td>
                                        <span class="device-badge">
                                            {{ $reading->device_id }}
                                        </span>
                                    </td>

                                    <td class="temperature-data">
                                        {{ number_format($reading->temperature, 2) }} °C
                                    </td>

                                    <td class="humidity-data">
                                        {{ number_format($reading->humidity, 2) }} %
                                    </td>

                                    <td>
                                        {{ $reading->recorded_at
                                            ? $reading->recorded_at->format('M d, Y h:i:s A')
                                            : 'Not provided' }}
                                    </td>

                                    <td>
                                        {{ $reading->created_at->format('M d, Y h:i:s A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif

        </section>


        <!-- Footer -->
        <footer class="dashboard-footer">
            <span>SMART GREENHOUSE MONITORING SYSTEM</span>
            <span>ESP32 // LARAVEL // MYSQL</span>
        </footer>

    </main>

</body>
</html>