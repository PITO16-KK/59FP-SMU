<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQI Monitor - Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            min-height: 100vh;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: rgba(30, 27, 75, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 40px;
            padding: 10px;
        }

        .logo-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .menu-item:hover {
            background: rgba(139, 92, 246, 0.2);
            color: white;
        }

        .menu-item.active {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            color: white;
        }

        .menu-item i {
            font-size: 18px;
        }

        .logout {
            margin-top: auto;
            color: #ef4444;
        }

        .logout:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-bar {
            position: relative;
            width: 300px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 40px 12px 20px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            outline: none;
        }

        .search-bar input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .page-title {
            color: white;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 30px;
        }

        /* Cards */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            /* Selalu 4 card per baris */
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .card-value {
            font-size: 36px;
            font-weight: bold;
            margin: 15px 0;
        }

        .card-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        table {
            width: 100%;
            color: white;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-good {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        .status-moderate {
            background: rgba(251, 191, 36, 0.2);
            color: #fde047;
        }

        .status-bad {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .motivation-card {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .motivation-text {
            font-size: 20px;
            font-style: italic;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .motivation-author {
            text-align: right;
            opacity: 0.8;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: white;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            outline: none;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
        }

        .hidden {
            display: none !important;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.5);
        }

        .motivasi-card {
            padding: 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            color: #fff;
            text-align: left;
        }

        .motivasi-card small {
            opacity: 0.8;
        }

        .city-search {
            display: flex;
            gap: 10px;
            max-width: 400px;
            margin-top: 20px;
        }

        .city-search input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .city-search input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .city-search button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .city-search button:hover {
            background-color: #0056b3;
        }

        .aqi-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            max-width: 400px;
            margin-top: 1rem;
        }

        .aqi-card-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .aqi-card-status {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .aqi-card-temp {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .aqi-card-components {
            list-style: none;
            padding-left: 0;
            margin-bottom: 10px;
        }

        .aqi-card-components li {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .aqi-card-btn {
            background: #4CAF50;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: 0.2s;
        }

        .aqi-card-btn:hover {
            background: #45a049;
        }

        /* Container kartu kota favorit */
        #favorite-cities {
            display: flex;
            flex-wrap: wrap;
            /* biar kartu pindah ke baris baru kalau tidak muat */
            gap: 15px;
            /* jarak antar kartu */
            justify-content: flex-start;
            /* bisa diganti center kalau mau di tengah */
            margin-top: 20px;
        }

        /* Kartu kota favorit */
        .favorite-city-card {
            flex: 1 1 200px;
            /* minimal 200px, fleksibel */
            max-width: 300px;
            /* tetap batasi maksimal lebar */
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 15px 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        /* Hover effect */
        .favorite-city-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* Judul kota */
        .favorite-city-card h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: capitalize;
            /* huruf pertama besar */
        }

        /* Info AQI, suhu, kelembaban */
        .favorite-city-card p {
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Optional: badge kecil untuk AQI */
        .favorite-city-card .aqi-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        /* Contoh warna badge berdasarkan status AQI */
        .favorite-city-card .baik {
            background-color: #4caf50;
        }

        .favorite-city-card .sedang {
            background-color: #ffeb3b;
            color: #000;
        }

        .favorite-city-card .tidak-sehat {
            background-color: #ff9800;
        }

        .favorite-city-card .buruk {
            background-color: #f44336;
        }

        .favorite-city-card .sangat-buruk {
            background-color: #9c27b0;
        }

        /* Container lokasi */
        .location-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        /* Item notifikasi */
        .location-item {
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            color: white;
            max-width: 500px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        /* Hover effect */
        .location-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* Status aman */
        .location-item.safe {
            background-color: rgba(46, 204, 113, 0.2);
            /* hijau muda */
            border-color: rgba(46, 204, 113, 0.5);
        }

        /* Status peringatan */
        .location-item.warning {
            background-color: rgba(231, 76, 60, 0.2);
            /* merah muda */
            border-color: rgba(231, 76, 60, 0.5);
        }

        /* Judul notifikasi */
        .location-item h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
        }

        /* Isi pesan */
        .location-item p {
            margin: 0;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Responsive kecil */
        @media (max-width: 600px) {
            .location-item {
                max-width: 100%;
                padding: 12px 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <div class="logo-icon">⚙</div>
                <span>AQI Monitor</span>
            </div>

            <nav>
                <a href="#" class="menu-item active" data-page="dashboard"><span>📊</span>Dashboard Utama</a>
                <a href="#" class="menu-item" data-page="peta"><span>📍</span>Peta Kualitas Udara</a>
                <a href="#" class="menu-item" data-page="analisis"><span>📈</span>Analisis Data</a>
                <a href="#" class="menu-item" data-page="perbandingan"><span>⚖️</span>Perbandingan</a>
                <a href="#" class="menu-item" data-page="notifikasi"><span>🔔</span>Notifikasi</a>
                <a href="#" class="menu-item" data-page="pengaturan"><span>👤</span>Pengaturan Akun</a>
                <a href="#" class="menu-item logout" data-page="logout"><span>🚪</span>Log Out</a>
            </nav>
        </div>

        <div class="main-content">

            <div class="header">
                <div class="search-bar">

                </div>
                <div class="user-info">
                    <button class="notification-btn">🔔</button>
                    <div class="user-profile">
                        <span><?= $_SESSION['user_name']; ?></span>
                        <div class="user-avatar">
                            <?= strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="dashboard-page" class="page-content">
                <h1 class="page-title">Dashboard Utama</h1>

                <div id="motivasi" style="margin-bottom: 1rem; margin-top: 1rem;"></div>

                <div class="section-title" style="color: white; font-size: 20px; margin-bottom: 20px;">
                    Ringkasan Analisis Kampus Real-time
                </div>

                <div class="cards-grid">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);">🌬️</div>
                            <div class="card-label">AQI Rata-rata Kampus</div>
                        </div>
                        <div class="card-value" id="avgAqi">···</div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);">🌡️</div>
                            <div class="card-label">Suhu Rata-rata</div>
                        </div>
                        <div class="card-value" id="avgTemp">···</div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);">💧</div>
                            <div class="card-label">Kelembapan Rata-rata</div>
                        </div>
                        <div class="card-value" id="avgHum">···</div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);">🏃</div>
                            <div class="card-label">Rekomendasi Aktivitas</div>
                        </div>
                        <div id="recommendation-value">...</div>
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="aqiTrendChart" style="width:100%; height:320px;"></canvas>
                </div>

                <div class="table-container">
                    <h3 style="color: white; margin-bottom: 20px;">Status Kualitas Udara</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Lokasi</th>
                                <th>AQI</th>
                                <th>Suhu</th>
                                <th>Kelembapan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="sensorTableBody">
                            <tr>
                                <td colspan="5" style="text-align:center;">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="peta-page" class="page-content hidden">
                <h1 class="page-title">Peta Kualitas Udara</h1>
                <p class="page-subtitle">Visualisasi kualitas udara di kampus</p>
                <div class="chart-container" style="height: 500px;">
                    <div>🗺️ Peta interaktif akan ditampilkan di sini</div>
                </div>

                <!-- <pre><?= var_dump($city_favorites) ?></pre> -->

                <h1 class="page-title">Kualitas Udara Kota Favorit</h1>

                <!-- Card Kota Favorit -->
                <div id="favorite-cities" class="favorite-cities mt-4">
                    <?php foreach ($city_favorites as $city): ?>
                        <div class="favorite-city-card"
                            data-lat="<?= $city['latitude'] ?>"
                            data-lon="<?= $city['longitude'] ?>"
                            data-city="<?= htmlspecialchars($city['city']) ?>">
                            <p>Loading...</p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pencarian kota -->
                <div class="city-search mt-4">
                    <input type="text" id="city-input" placeholder="Cari kota lain" />
                    <button id="search-city">Cari</button>
                </div>

                <!-- Hasil AQI -->
                <div id="city-result" class="mt-3"></div>

                <?php $userId = $_SESSION['user_id'] ?? null; ?>
            </div>

            <script>
                const userId = <?= $userId ?? 'null' ?>;
                const searchBtn = document.getElementById('search-city');
                const cityInput = document.getElementById('city-input');
                const cityResult = document.getElementById('city-result');

                searchBtn.addEventListener('click', async () => {
                    const city = cityInput.value.trim();
                    if (!city) return alert('Masukkan nama kota');

                    // fetch data AQI
                    try {
                        const res = await fetch(`/sistem_monitoring_udara/public/city-air-condition?city=${encodeURIComponent(city)}`);
                        const data = await res.json();

                        if (data.error) {
                            cityResult.innerHTML = `<p style="color:red">${data.error}</p>`;
                            return;
                        }

                        // tampilkan hasil dalam bentuk card
                        cityResult.innerHTML = `
                            <div class="aqi-card">
                                <h4 class="aqi-card-title">${data.city}</h4>
                                <p class="aqi-card-status">Status AQI: <strong>${data.status}</strong> (AQI: ${data.aqi})</p>
                                <p class="aqi-card-temp">Temperature: ${data.temperature}°C, Humidity: ${data.humidity}%</p>
                                <button id="add-favorite" class="aqi-card-btn">Add to Favorites</button>
                            </div>
                        `;

                        // tombol add
                        document.getElementById('add-favorite').addEventListener('click', async () => {
                            try {
                                const postRes = await fetch('/sistem_monitoring_udara/public/favorite-location', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        user_id: userId,
                                        city: data.city,
                                        latitude: data.coord.lat,
                                        longitude: data.coord.lon
                                    })
                                });
                                const postData = await postRes.json();
                                if (postRes.ok) {
                                    alert('Berhasil ditambahkan ke favorit!');
                                } else {
                                    alert(postData.error || 'Gagal menambahkan');
                                }
                            } catch (err) {
                                console.error(err);
                                alert('Gagal menambahkan ke favorit');
                            }
                        });

                    } catch (err) {
                        console.error(err);
                        cityResult.innerHTML = `<p style="color:red">Terjadi kesalahan</p>`;
                    }
                });
            </script>

            <div id="analisis-page" class="page-content hidden">
                <h1 class="page-title">Analisis Data</h1>
                <p class="page-subtitle">Analisis mendalam data kualitas udara</p>
                <div class="cards-grid">

                </div>
            </div>

            <div id="perbandingan-page" class="page-content hidden">
                <h1 class="page-title">Perbandingan</h1>
                <p class="page-subtitle">Bandingkan data antar lokasi dan waktu</p>
                <div class="table-container">
                    <h3 style="color: white; margin-bottom: 20px;">Perbandingan Antar Lokasi</h3>
                    <div class="chart-container">
                        <canvas id="aqiCompareChart" style="width:100%; height:320px;"></canvas>
                    </div>
                </div>

            </div>

            <div id="notifikasi-page" class="page-content hidden">
                <h1 class="page-title">Notifikasi</h1>
                <p class="page-subtitle">Peringatan dan pemberitahuan</p>
                <div class="location-list"> </div>
            </div>

            <script>
                const notifContainer = document.querySelector("#notifikasi-page .location-list");

                fetch("/sistem_monitoring_udara/public/avg-aqi")
                    .then(response => response.json())
                    .then(data => {
                        const avgAqi = data.average_aqi; // ambil average_aqi saja
                        let html = "";

                        if (avgAqi <= 50) {
                            html = `
                <div class="location-item safe">
                    <h3>😊 Aman</h3>
                    <p>Udara bersih, aman untuk aktivitas luar ruangan.</p>
                </div>
            `;
                        } else {
                            let pesan = "";

                            if (avgAqi <= 100) {
                                pesan = "Udara sedang. Aman, tetapi gunakan masker jika sensitif.";
                            } else if (avgAqi <= 150) {
                                pesan = "Tidak sehat bagi kelompok sensitif. Batasi aktivitas luar ruangan.";
                            } else if (avgAqi <= 200) {
                                pesan = "Tidak sehat. Semua orang disarankan mengurangi aktivitas luar ruangan.";
                            } else if (avgAqi <= 300) {
                                pesan = "Sangat tidak sehat. Hindari aktivitas luar ruangan.";
                            } else if (avgAqi <= 500) {
                                pesan = "Berbahaya! Tetap di dalam ruangan, gunakan air purifier jika ada.";
                            } else {
                                pesan = "AQI tidak valid.";
                            }

                            html = `
                <div class="location-item warning">
                    <h3>⚠️ Peringatan Kesehatan</h3>
                    <p>${pesan}</p>
                </div>
            `;
                        }

                        notifContainer.innerHTML = html;
                    })
                    .catch(err => {
                        console.error("Gagal fetch data AQI:", err);
                        notifContainer.innerHTML = `<p style="color:red">Gagal mengambil data kualitas udara.</p>`;
                    });
            </script>

            <div id="pengaturan-page" class="page-content hidden">
                <h1 class="page-title">Pengaturan Akun</h1>
                <p class="page-subtitle">Kelola profil dan preferensi Anda</p>

                <div class="card">
                    <h3 style="margin-bottom: 20px;">Informasi Profil</h3>
                    <div class="form-group"><label>Nama Lengkap</label>
                        <input type="text" id="profileName" placeholder="Nama lengkap..." value="<?= $_SESSION['user_name'] ?? '' ?>">
                    </div>

                    <div class="form-group"><label>Email</label>
                        <input type="email" id="profileEmail" placeholder="email@example.com" value="<?= $_SESSION['user_email'] ?? '' ?>">
                    </div>

                    <div class="form-group"><label>Role</label>
                        <input type="text" id="profileRole" value="<?= $_SESSION['user_role'] ?? '' ?>" readonly>
                    </div>

                    <button id="btnSaveProfile" class="btn btn-primary">💾 Simpan Perubahan</button>
                </div>

                <div class="card" style="margin-top: 20px;">
                    <h3 style="margin-bottom: 20px;">Ubah Password</h3>
                    <div class="form-group"><label>Password Lama</label>
                        <input type="password" id="oldPassword" placeholder="Password lama...">
                    </div>
                    <div class="form-group"><label>Password Baru</label>
                        <input type="password" id="newPassword" placeholder="Password baru...">
                    </div>
                    <div class="form-group"><label>Konfirmasi Password</label>
                        <input type="password" id="confirmPassword" placeholder="Konfirmasi password...">
                    </div>
                    <button id="btnChangePass" class="btn btn-primary">🔒 Ubah Password</button>
                </div>
            </div>


            <div id="logout-page" class="page-content hidden">
                <div class="card" style="text-align: center; padding: 60px;">
                    <div style="font-size: 64px; margin-bottom: 20px;">👋</div>
                    <h2 style="margin-bottom: 15px;">Yakin ingin keluar?</h2>
                    <p style="margin-bottom: 30px; opacity: 0.7;">Anda akan diarahkan ke halaman login</p>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button class="btn btn-primary" onclick="confirmLogout()">Ya, Keluar</button>
                        <button class="btn" style="background: rgba(255,255,255,0.1); color: white;" onclick="cancelLogout()">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let motivasiList = <?= json_encode($motivasi); ?>;
        document.addEventListener("DOMContentLoaded", function() {
            const favoriteCards = document.querySelectorAll('.favorite-city-card');

            favoriteCards.forEach(card => {
                const lat = card.dataset.lat;
                const lon = card.dataset.lon;
                const cityName = card.dataset.city;

                fetch(`/sistem_monitoring_udara/public/air-condition?lat=${lat}&lon=${lon}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            card.innerHTML = `<p style="color:red">${data.error}</p>`;
                        } else {
                            card.innerHTML = `
                        <h4>${cityName}</h4>
                        <p>AQI: <strong>${data.aqi} (${data.status})</strong></p>
                        <p>Temperature: ${data.temperature}°C</p>
                        <p>Humidity: ${data.humidity}%</p>
                    `;
                        }
                    })
                    .catch(err => {
                        card.innerHTML = `<p style="color:red">Gagal memuat data</p>`;
                        console.error(err);
                    });
            });

            if (!motivasiList || motivasiList.length === 0) return;

            // Urutkan berdasarkan ID jika mau
            motivasiList.sort((a, b) => a.id - b.id);

            let currentIndex = 0;

            function showMotivasi() {
                let item = motivasiList[currentIndex];
                document.getElementById("motivasi").innerHTML = `
            <div class="motivasi-card">
                <p>"${item.kutipan}"</p>
                <small>- ${item.penulis}</small>
            </div>
        `;
                currentIndex = (currentIndex + 1) % motivasiList.length;
            }

            showMotivasi(); // tampil pertama
            setInterval(showMotivasi, 5000); // ganti tiap 5 detik
        });

        // ===== Update Profile =====
        document.getElementById("btnSaveProfile").addEventListener("click", () => {
            const btn = document.getElementById("btnSaveProfile");
            btn.disabled = true;
            btn.innerHTML = "⏳ Menyimpan...";

            const name = document.getElementById("profileName").value;
            const email = document.getElementById("profileEmail").value;

            fetch("sistem_monitoring_udara/public/edit-profile", {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    credentials: "include",
                    body: JSON.stringify({
                        name,
                        email,
                        password: null
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);

                    if (data.status) {
                        window.location.reload();
                    }
                })
                .catch(err => console.error("Gagal update profil:", err))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = "💾 Simpan Perubahan";
                });
        });
        document.getElementById("btnChangePass").addEventListener("click", () => {
            const oldPass = document.getElementById("oldPassword").value;
            const newPass = document.getElementById("newPassword").value;
            const confirmPass = document.getElementById("confirmPassword").value;

            if (newPass !== confirmPass) {
                alert("Password baru dan konfirmasi tidak sama!");
                return;
            }

            fetch("sistem_monitoring_udara/public/edit-profile", {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    credentials: "include",
                    body: JSON.stringify({
                        password: newPass,
                        oldPassword: oldPass
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status) window.location.reload();
                })
                .catch(err => console.error("Gagal ubah password:", err));
        });

        const menuItems = document.querySelectorAll(".menu-item");
        const pages = document.querySelectorAll(".page-content");

        menuItems.forEach(item => {
            item.addEventListener("click", () => {
                const targetPage = item.dataset.page;

                // aktifkan menu
                menuItems.forEach(i => i.classList.remove("active"));
                item.classList.add("active");

                // tampilkan page yg sesuai
                pages.forEach(p => p.classList.add("hidden"));
                document.getElementById(`${targetPage}-page`).classList.remove("hidden");

                // logout
                if (targetPage === "logout") {
                    document.getElementById("logout-page").classList.remove("hidden");
                }
            });
        });

        function confirmLogout() {
            fetch('sistem_monitoring_udara/public/logout', {
                    method: 'GET',
                    credentials: 'include', // penting kalau pakai session/cookie
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => {
                    if (res.ok) {
                        alert('Anda telah logout. Terima kasih!');
                        window.location.href = 'sistem_monitoring_udara/public/login'; // redirect ke halaman login
                    } else {
                        alert('Gagal logout, coba lagi.');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Terjadi kesalahan saat logout.');
                });

        }

        function cancelLogout() {
            document.querySelector(".menu-item[data-page='dashboard']").click();
        }

        // =============================
        // CHART TREND AQI REALTIME
        // =============================
        // ====== GRAFIK TREND AQI ======
        const ctx = document.getElementById('aqiTrendChart').getContext('2d');

        const initialLabels = Array.from({
            length: 20
        }, () => "");
        const initialData = Array.from({
            length: 20
        }, () => 0);

        const aqiChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: initialLabels,
                datasets: [{
                        type: 'line',
                        label: 'Trend AQI',
                        data: initialData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'Nilai AQI',
                        data: initialData,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        yAxisID: 'y',
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Waktu"
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // ====== GRAFIK PERBANDINGAN AQI PER LOKASI ======
        const ctxCompare = document.getElementById("aqiCompareChart").getContext("2d");

        const aqiCompareChart = new Chart(ctxCompare, {
            type: "bar",
            data: {
                labels: [],
                datasets: [{
                    label: "AQI per Lokasi",
                    data: [],
                    borderColor: "rgba(54, 162, 235, 1)",
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    barPercentage: 0.7, // Batang lebar full
                    categoryPercentage: 0.6 // Panel kategori juga full
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: "white", // Label legend warna putih
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: "white" // Teks angka Y putih
                        },
                        grid: {
                            color: "rgba(255,255,255,0.2)" // garis grid abu transparan
                        }
                    },
                    x: {
                        ticks: {
                            color: "white" // Nama lokasi putih
                        },
                        grid: {
                            display: false // Matikan garis vertikal biar rapi
                        }
                    }
                }
            }
        });

        // ====== FETCH RATA-RATA AQI UNTUK TREND ======
        async function fetchAvgAQI() {
            try {
                const res = await fetch("sistem_monitoring_udara/public/avg-aqi");
                const data = await res.json();

                if (!data || !data.average_aqi) return;

                const now = new Date().toLocaleTimeString();

                aqiChart.data.labels.push(now);
                aqiChart.data.datasets[0].data.push(data.average_aqi);
                aqiChart.data.datasets[1].data.push(data.average_aqi);

                if (aqiChart.data.labels.length > 20) {
                    aqiChart.data.labels.shift();
                    aqiChart.data.datasets[0].data.shift();
                    aqiChart.data.datasets[1].data.shift();
                }

                aqiChart.update();
            } catch (err) {
                console.error("Fetch error:", err);
            }
        }

        setInterval(fetchAvgAQI, 10000);


        // ====== FETCH SEMUA DATA & UPDATE TABEL + CHART LOKASI ======
        function fetchAllAirQuality() {
            fetch("sistem_monitoring_udara/public/all-air-quality")
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.locations) return;

                    let totalAqi = 0,
                        totalTemp = 0,
                        totalHumidity = 0;
                    let count = data.locations.length;

                    const tbody = document.getElementById("sensorTableBody");
                    tbody.innerHTML = "";

                    data.locations.forEach(loc => {
                        totalAqi += loc.aqi ?? 0;
                        totalTemp += loc.temperature ?? 0;
                        totalHumidity += loc.humidity ?? 0;

                        let statusClass = "status-good";
                        let statusText = "Baik";

                        if (loc.aqi > 100) {
                            statusClass = "status-danger";
                            statusText = "Tidak Sehat";
                        } else if (loc.aqi > 50) {
                            statusClass = "status-warning";
                            statusText = "Sedang";
                        }

                        tbody.innerHTML += `
                    <tr>
                        <td>${loc.name}</td>
                        <td>${loc.aqi}</td>
                        <td>${loc.temperature}°C</td>
                        <td>${loc.humidity}%</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    </tr>`;
                    });

                    let avgAqi = (totalAqi / count).toFixed(2);
                    let avgTemp = (totalTemp / count).toFixed(2);
                    let avgHum = (totalHumidity / count).toFixed(2);
                    let recommendation_value = "";

                    if (avgHum <= 50) {
                        recommendation_value = "Kualitas udara baik. Aman untuk beraktivitas di luar ruangan.";
                    } else if (avgHum <= 100) {
                        recommendation_value = "Kualitas udara sedang. Kelompok sensitif harus berhati-hati.";
                    } else if (avgHum <= 150) {
                        recommendation_value = "Tidak sehat untuk kelompok sensitif. Kurangi aktivitas berat di luar.";
                    } else if (avgHum <= 200) {
                        recommendation_value = "Tidak sehat. Disarankan menggunakan masker jika di luar.";
                    } else if (avgHum <= 300) {
                        recommendation_value = "Sangat tidak sehat. Hindari aktivitas luar ruangan.";
                    } else {
                        recommendation_value = "Berbahaya. Tetap di dalam ruangan & gunakan penjernih udara.";
                    }

                    document.getElementById("avgAqi").innerText = avgAqi;
                    document.getElementById("avgTemp").innerText = avgTemp + "°C";
                    document.getElementById("avgHum").innerText = avgHum + "%";
                    document.getElementById("recommendation-value").innerText = recommendation_value;

                    let aqiStatus =
                        avgAqi <= 50 ? "Baik" :
                        avgAqi <= 100 ? "Sedang" :
                        avgAqi <= 150 ? "Tidak Sehat untuk Sensitif" :
                        avgAqi <= 200 ? "Tidak Sehat" :
                        avgAqi <= 300 ? "Sangat Tidak Sehat" :
                        "Berbahaya";

                    // ==== UPDATE GRAFIK PERBANDINGAN (PAKAI name BUKAN location) ====
                    aqiCompareChart.data.labels = data.locations.map(loc => loc.name);
                    aqiCompareChart.data.datasets[0].data = data.locations.map(loc => loc.aqi ?? 0);
                    aqiCompareChart.update();
                })
                .catch(err => console.error("Fetch Error:", err));
        }

        fetchAllAirQuality();
        setInterval(fetchAllAirQuality, 10000);

        // ===== FETCH DATA DARI API =====
        async function loadAirQualityData() {
            try {
                const res = await fetch("sistem_monitoring_udara/public/all-air-quality");
                const data = await res.json();

                if (data && data.locations) {
                    renderMap(data.locations);
                    renderAnalysisCards(data.locations);
                } else {
                    console.error("Data lokasi tidak ditemukan");
                }
            } catch (error) {
                console.error("Gagal fetch API:", error);
            }
        }

        // ====== RENDER MAP ======
        function renderMap(locations) {
            const mapContainer = document.querySelector("#peta-page .chart-container");
            mapContainer.innerHTML = `<div id="campusMap" style="width:100%; height:100%; border-radius:10px;"></div>`;

            // Gunakan lokasi pertama sebagai center
            const centerLat = parseFloat(locations[0].lat);
            const centerLon = parseFloat(locations[0].lon);

            const map = L.map("campusMap").setView([centerLat, centerLon], 18);

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19
            }).addTo(map);

            locations.forEach(loc => {

                let lat = parseFloat(loc.lat);
                let lon = parseFloat(loc.lon);

                // Tentukan warna marker
                let color = "#00e676"; // hijau
                if (loc.aqi > 100) color = "#ff1744";
                else if (loc.aqi > 50) color = "#ffeb3b";

                // Circle marker
                L.circleMarker([lat, lon], {
                        radius: 12,
                        fillColor: color,
                        color: "#ffffff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.9
                    })
                    .addTo(map)
                    .bindPopup(`
            <b>${loc.name.toUpperCase()}</b><br>
            AQI: <b>${loc.aqi}</b><br>
            Status: <span>${loc.status}</span>
        `);
            });
        }

        // ====== RENDER ANALISIS CARD ======
        function renderAnalysisCards(locations) {
            const cardContainer = document.querySelector("#analisis-page .cards-grid");
            cardContainer.innerHTML = "";

            locations.forEach(loc => {
                cardContainer.innerHTML += `
            <div class="card">
                <h3>${loc.name.toUpperCase()}</h3>
                <p>Status Udara</p>
                <div class="value">${loc.aqi} - ${loc.status}</div>
            </div>
        `;
            });
        }

        // ======= LOAD DATA SAAT AWAL ======
        loadAirQualityData();
    </script>

</body>

</html>
