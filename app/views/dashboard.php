<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Quality Dashboard - UBP Karawang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            background: #f8fafc;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }

        .header-card h1 {
            color: #1e293b;
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
        }

        .header-card .subtitle {
            color: var(--secondary);
            margin: 0.5rem 0 0 0;
        }

        .status-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .status-card .aqi-value {
            font-size: 3.5rem;
            font-weight: 700;
            margin: 0;
        }

        .status-card .status-text {
            font-size: 1.5rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .map-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        #map {
            height: 600px;
            border-radius: 12px;
            overflow: hidden;
        }

        .legend {
            background: rgba(255, 255, 255, 0.98);
            padding: 15px;
            line-height: 24px;
            border-radius: 12px;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 2px solid #e2e8f0;
        }

        .legend strong {
            font-size: 14px;
            color: #1e293b;
            display: block;
            margin-bottom: 8px;
        }

        .legend i {
            width: 14px;
            height: 14px;
            float: left;
            margin-right: 10px;
            opacity: 0.9;
            border-radius: 3px;
        }

        /* Loading Spinner */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(102, 126, 234, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loading-overlay.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            font-size: 1.2rem;
            margin-top: 1.5rem;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-card h1 {
                font-size: 1.5rem;
            }
            
            .status-card .aqi-value {
                font-size: 2.5rem;
            }
            
            #map {
                height: 400px;
            }
        }

        /* Custom Leaflet Popup */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .leaflet-popup-content {
            margin: 12px;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Memuat data kualitas udara...</div>
    </div>

    <div class="dashboard-container">
        <!-- Header -->
        <div class="header-card">
            <h1>🌍 Air Quality Dashboard</h1>
            <p class="subtitle">Real-time monitoring UBP Karawang</p>
        </div>

        <!-- Status Card -->
        <div class="status-card" id="statusCard" style="display: none;">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <p class="aqi-value" id="aqiValue">-</p>
                    <small style="opacity: 0.8;">Air Quality Index</small>
                </div>
                <div class="col-md-8">
                    <h3 class="status-text" id="statusText">-</h3>
                    <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Kondisi udara di lokasi pemantauan</p>
                </div>
            </div>
        </div>

        <!-- Map Card -->
        <div class="map-card">
            <div id="map"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var lat = -6.32386855;
        var lon = 107.300928628235;

        // Inisialisasi map
        var map = L.map('map').setView([lat, lon], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Animasi Fly ke lokasi
        setTimeout(() => {
            map.flyTo([lat, lon], 19, {
                animate: true,
                duration: 1
            });
        }, 800);

        // Ambil data API lokal
        fetch(`http://localhost:8000/air?lat=${lat}&lon=${lon}`)
            .then(res => res.json())
            .then(data => {
                const {
                    aqi,
                    status,
                    components
                } = data;

                // Update status card
                document.getElementById('aqiValue').textContent = aqi;
                document.getElementById('statusText').textContent = status;
                document.getElementById('statusCard').style.display = 'block';

                // Warna komponen polutan
                const colors = {
                    co: "#ef4444",
                    no: "#3b82f6",
                    no2: "#8b5cf6",
                    o3: "#10b981",
                    so2: "#f59e0b",
                    pm2_5: "#92400e",
                    pm10: "#06b6d4",
                    nh3: "#ec4899"
                };

                const offset = 0.00035; // jarak radius
                const keys = Object.keys(components);

                // HANYA titik komponen (acak, tidak melingkar rapi)
                keys.forEach((key) => {
                    const value = components[key];
                    const color = colors[key];

                    const randomAngle = Math.random() * 2 * Math.PI; // random arah
                    const randomDist = offset * (0.4 + Math.random() * 0.6); // random jarak dari pusat

                    const markerLat = lat + Math.cos(randomAngle) * randomDist;
                    const markerLon = lon + Math.sin(randomAngle) * randomDist;

                    L.circleMarker([markerLat, markerLon], {
                            radius: 9,
                            color: 'white',
                            weight: 2,
                            fillColor: color,
                            fillOpacity: 0.9
                        })
                        .addTo(map)
                        .bindPopup(`
                            <div style="text-align: center;">
                                <b style="color: ${color}; font-size: 16px;">${key.toUpperCase()}</b><br>
                                <span style="color: #64748b;">Konsentrasi</span><br>
                                <b style="font-size: 18px;">${value}</b>
                            </div>
                        `);
                });

                // LEGEND
                const legend = L.control({
                    position: "bottomright"
                });
                legend.onAdd = function(map) {
                    const div = L.DomUtil.create("div", "legend");
                    div.innerHTML = `<strong>🔬 Polutan Udara</strong><br>`;
                    for (const key in colors) {
                        div.innerHTML += `<i style="background:${colors[key]}"></i>${key.toUpperCase()}<br>`;
                    }
                    return div;
                };
                legend.addTo(map);

                // Hide loading overlay
                setTimeout(() => {
                    document.getElementById('loadingOverlay').classList.add('hidden');
                }, 500);
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loadingOverlay').innerHTML = `
                    <div style="text-align: center; color: white;">
                        <h3>⚠️ Terjadi Kesalahan</h3>
                        <p>Tidak dapat mengambil data kualitas udara</p>
                        <button class="btn btn-light mt-3" onclick="location.reload()">Coba Lagi</button>
                    </div>
                `;
            });
    </script>
</body>

</html>