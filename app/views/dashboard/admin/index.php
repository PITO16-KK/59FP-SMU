<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AQI Monitor - Dashboard</title>
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e1e2e 0%, #2d1b4e 100%);
            color: #fff;
            min-height: 100vh;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: rgba(30, 20, 60, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #fff;
        }

        .logo i {
            font-size: 28px;
            color: #8b5cf6;
        }

        .menu {
            flex: 1;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }

        .menu-item:hover {
            background: rgba(139, 92, 246, 0.2);
            color: #fff;
        }

        .menu-item.active {
            background: #8b5cf6;
            color: #fff;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        .menu-separator {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 20px 0;
        }

        .logout {
            color: #ef4444;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            outline: none;
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification {
            position: relative;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .notification .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #8b5cf6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .page-title {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(139, 92, 246, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
            color: #8b5cf6;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #fff;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 40px;
        }

        .chart-placeholder {
            height: 300px;
            background: rgba(139, 92, 246, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.5);
        }

        .table-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            outline: none;
        }

        .form-input:focus {
            border-color: #8b5cf6;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #8b5cf6;
            color: #fff;
        }

        .btn-primary:hover {
            background: #7c3aed;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .map-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar .menu-item span {
                display: none;
            }

            .logo span {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <i class="fas fa-wind"></i>
                <span>AQI Monitor</span>
            </div>

            <nav class="menu">
                <a href="#" class="menu-item active" data-page="dashboard">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item" data-page="peta-sensor">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Peta Kualitas Udara</span>
                </a>
                <a href="#" class="menu-item" data-page="analisis-data">
                    <i class="fas fa-chart-line"></i>
                    <span>Analisis Data</span>
                </a>

                <div class="menu-separator"></div>

                <a href="#" class="menu-item" data-page="manajemen-pengguna">
                    <i class="fas fa-users"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="#" class="menu-item" data-page="manajemen-lokasi">
                    <i class="fas fa-microchip"></i>
                    <span>Manajemen Lokasi</span>
                </a>

                <div class="menu-separator"></div>

                <a href="#" class="menu-item" data-page="profil-admin">
                    <i class="fas fa-user"></i>
                    <span>Profil Admin</span>
                </a>
                <a href="#" class="menu-item" data-page="help-support">
                    <i class="fas fa-question-circle"></i>
                    <span>Help & Support</span>
                </a>
                <a href="/logout" class="menu-item logout" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <input type="text" placeholder="Cari Sensor atau Pengguna" />
                    <i class="fas fa-search"></i>
                </div>

                <div class="user-section">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-info">
                        <span>Administrator</span>
                        <div class="user-avatar">A</div>
                    </div>
                </div>
            </header>

            <div id="dashboard" class="content-section active">
                <h1 class="page-title">Dashboard Administrator</h1>
                <p class="page-subtitle" id="aqi-status">Memuat data...</p>

                <h2 class="section-title">Ringkasan Real-time</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-wind"></i>
                        </div>
                        <div class="stat-label">AQI Rata-rata</div>
                        <div class="stat-value" id="avg-aqi">...</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-label">Total Pengguna</div>
                        <div class="stat-value"><?= $totalUsers ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <div class="stat-label">Titik Lokasi</div>
                        <div class="stat-value"><?= $totalLocations ?></div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 style="margin-bottom: 20px;">Grafik Tren AQI Rata-rata</h3>
                    <canvas id="aqiTrendChart" height="100"></canvas>
                </div>
            </div>

            <div id="peta-sensor" class="content-section">
                <h1 class="page-title">Peta Kualitas Udara</h1>
                <p class="page-subtitle">Monitoring lokasi pemantauan kualitas udara</p>

                <div class="map-container" style="height: 400px;">
                    <div id="map" style="width: 100%; height: 100%;"></div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>AQI Terakhir</th>
                                <th>Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody id="aqi-table-body">
                            <tr>
                                <td colspan="4" style="text-align:center;">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <div id="analisis-data" class="content-section">
                <h1 class="page-title">Analisis Data</h1>
                <p class="page-subtitle">Analisis mendalam kualitas udara</p>

                <div id="location-cards" class="cards-container" style="display: flex; gap: 20px; flex-wrap: wrap;"></div>

                <!-- Card Kesimpulan Keseluruhan -->
                <div id="overall-summary" style="margin-top:20px;"></div>
            </div>

            <div id="manajemen-pengguna" class="content-section">
                <h1 class="page-title">Manajemen Pengguna</h1>
                <p class="page-subtitle">Kelola pengguna sistem</p>

                <div class="table-container">
                    <div style="margin-bottom: 20px;">
                        <button id="btn-add-user" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Tambah Pengguna
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body">
                            <!-- Data user akan ter-render di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal Tambah/Edit User -->
            <div id="user-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background: rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000;">
                <div style="background:#fff; padding:25px 30px; border-radius:12px; width:400px; max-width:90%; 
        box-shadow:0 8px 20px rgba(0,0,0,0.25); font-family:Arial, sans-serif;">

                    <h3 id="modal-title" style="margin-bottom:20px; font-size:1.5rem; color:#333;">Tambah Pengguna</h3>

                    <form id="user-form">
                        <input type="hidden" id="user-id">

                        <div style="margin-bottom:15px;">
                            <label style="display:block; margin-bottom:6px; color:#555; font-weight:500;">Nama</label>
                            <input type="text" id="user-name" required
                                style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:0.95rem;">
                        </div>

                        <div style="margin-bottom:15px;">
                            <label style="display:block; margin-bottom:6px; color:#555; font-weight:500;">Email</label>
                            <input type="email" id="user-email" required
                                style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:0.95rem;">
                        </div>

                        <div style="margin-bottom:15px;">
                            <label style="display:block; margin-bottom:6px; color:#555; font-weight:500;">Role</label>
                            <select id="user-role" required
                                style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:0.95rem;">
                                <option value="admin">Admin</option>
                                <option value="dosen">Dosen</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>

                        <div style="margin-bottom:15px;">
                            <label style="display:block; margin-bottom:6px; color:#555; font-weight:500;">Status</label>
                            <select id="user-status" required
                                style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:0.95rem;">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                            <button type="button" id="btn-cancel"
                                style="padding:8px 16px; border:none; border-radius:6px; background:#e5e7eb; color:#333; cursor:pointer;">
                                Batal
                            </button>
                            <button type="submit"
                                style="padding:8px 16px; border:none; border-radius:6px; background:#3b82f6; color:#fff; cursor:pointer;">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="manajemen-lokasi" class="content-section">
                <h1 class="page-title">Manajemen Lokasi</h1>
                <p class="page-subtitle">Kelola lokasi pemantauan kualitas udara</p>

                <div class="table-container">
                    <div style="margin-bottom: 20px;">
                        <button class="btn btn-primary" id="btn-add-location">
                            <i class="fas fa-plus"></i> Tambah Lokasi
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="location-table-body">
                            <!-- Data lokasi akan di-render melalui JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal tambah/edit lokasi -->
            <div id="location-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background: rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000;">
                <div style="background:#fff; padding:25px 30px; border-radius:12px; width:400px; max-width:90%; 
        box-shadow:0 8px 20px rgba(0,0,0,0.25); font-family:Arial, sans-serif;">

                    <h3 id="location-modal-title" style="margin-bottom:20px; font-size:1.5rem; color:#333;">Tambah Lokasi</h3>

                    <form id="location-form">
                        <input type="hidden" id="location-id">

                        <div style="margin-bottom:15px;">
                            <label style="display:block; margin-bottom:6px; color:#555; font-weight:500;">Nama Lokasi</label>
                            <input type="text" id="location-name" required
                                style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:0.95rem;">
                        </div>

                        <div style="margin-bottom:15px;">
                            <label style="display:block; margin-bottom:6px; color:#555; font-weight:500;">Latitude</label>
                            <input type="text" id="location-lat" required
                                style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:0.95rem;">
                        </div>

                        <div style="margin-bottom:15px;">
                            <label style="display:block; margin-bottom:6px; color:#555; font-weight:500;">Longitude</label>
                            <input type="text" id="location-lon" required
                                style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:0.95rem;">
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                            <button type="button" id="location-cancel"
                                style="padding:8px 16px; border:none; border-radius:6px; background:#e5e7eb; color:#333; cursor:pointer;">
                                Batal
                            </button>
                            <button type="submit"
                                style="padding:8px 16px; border:none; border-radius:6px; background:#3b82f6; color:#fff; cursor:pointer;">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <div id="pengaturan-sistem" class="content-section">
                <h1 class="page-title">Pengaturan Sistem</h1>
                <p class="page-subtitle">Konfigurasi sistem</p>

                <div class="table-container">
                    <div class="form-group">
                        <label class="form-label">Nama Aplikasi</label>
                        <input type="text" class="form-input" value="AQI Monitor" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Interval Update (detik)</label>
                        <input type="number" class="form-input" value="60" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Notifikasi</label>
                        <input type="email" class="form-input" value="admin@aqimonitor.com" />
                    </div>
                    <button class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>

            <div id="profil-admin" class="content-section">
                <h1 class="page-title">Profil Admin</h1>
                <p class="page-subtitle">Kelola informasi profil Anda</p>

                <div class="table-container">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input" id="name"
                            value="<?= $_SESSION['user_name'] ?? '' ?>" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" id="email"
                            value="<?= $_SESSION['user_email'] ?? '' ?>" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-input" id="password"
                            placeholder="Kosongkan jika tidak ingin mengubah" />
                    </div>
                    <button class="btn btn-primary" id="btnUpdate">Update Profil</button>
                </div>
            </div>


            <div id="help-support" class="content-section">
                <h1 class="page-title">Help & Support</h1>
                <p class="page-subtitle">Bantuan dan dukungan</p>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3 style="margin-bottom: 10px;">Dokumentasi</h3>
                        <p style="color: rgba(255,255,255,0.6);">Panduan lengkap penggunaan sistem</p>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 style="margin-bottom: 10px;">Kontak Support</h3>
                        <p style="color: rgba(255,255,255,0.6);">support@aqimonitor.com</p>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h3 style="margin-bottom: 10px;">FAQ</h3>
                        <p style="color: rgba(255,255,255,0.6);">Pertanyaan yang sering diajukan</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.getElementById("btnUpdate").addEventListener("click", () => {
            const btnUpdate = document.getElementById("btnUpdate");
            const name = document.getElementById("name").value;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            // === Aktifkan Loading ===
            btnUpdate.disabled = true;
            btnUpdate.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Saving...`;

            fetch("/sistem_monitoring_udara/public/edit-profile", {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    credentials: "include",
                    body: JSON.stringify({
                        name,
                        email,
                        password: password !== "" ? password : null
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status) {
                        window.location.reload();
                    } else {
                        // kalo gagal
                        btnUpdate.disabled = false;
                        btnUpdate.innerHTML = "Update";
                    }
                })
                .catch(err => {
                    console.error("Gagal update profil:", err);
                    btnUpdate.disabled = false;
                    btnUpdate.innerHTML = "Update";
                });
        });


        const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1);

        const getAqiStatus = aqi => {
            if (aqi <= 50) return "Baik";
            if (aqi <= 100) return "Sedang";
            if (aqi <= 150) return "Tidak Sehat (Sensitif)";
            if (aqi <= 200) return "Tidak Sehat";
            if (aqi <= 300) return "Sangat Tidak Sehat";
            return "Berbahaya";
        };

        const getAqiColor = aqi => {
            if (aqi <= 50) return "#00FF00";
            if (aqi <= 100) return "#FFFF00";
            if (aqi <= 150) return "#FFA500";
            if (aqi <= 200) return "#FF0000";
            if (aqi <= 300) return "#800080";
            return "#800000";
        };

        const getActivityAdvice = aqi => {
            if (aqi <= 50) return {
                ok: "Aktivitas luar ruangan aman",
                avoid: "Tidak ada larangan khusus"
            };
            if (aqi <= 100) return {
                ok: "Bisa beraktivitas normal",
                avoid: "Sensitif sebaiknya kurangi aktivitas berat"
            };
            if (aqi <= 150) return {
                ok: "Aktivitas ringan di luar diperbolehkan",
                avoid: "Hindari olahraga berat di luar"
            };
            if (aqi <= 200) return {
                ok: "Batasi aktivitas di luar",
                avoid: "Hindari olahraga berat dan orang sensitif sebaiknya di dalam"
            };
            if (aqi <= 300) return {
                ok: "Hanya aktivitas penting di luar",
                avoid: "Hindari semua olahraga di luar, gunakan masker"
            };
            return {
                ok: "Tetap di dalam ruangan",
                avoid: "Hindari semua aktivitas luar"
            };
        };

        const fetchJSON = async url => {
            try {
                const res = await fetch(url);
                return await res.json();
            } catch (err) {
                console.error(`Gagal fetch ${url}:`, err);
                return null;
            }
        };

        /* =========================== DOM ELEMENTS =========================== */
        const cardsContainer = document.getElementById('location-cards');
        const overallSummaryEl = document.getElementById('overall-summary');
        const ctx = document.getElementById('aqiTrendChart').getContext('2d');
        const avgAqiEl = document.getElementById('avg-aqi');
        const aqiStatusEl = document.getElementById('aqi-status');
        const tableBody = document.getElementById('aqi-table-body');

        const tableBodyUser = document.getElementById('user-table-body');
        const userModal = document.getElementById('user-modal');
        const userForm = document.getElementById('user-form');
        const btnAddUser = document.getElementById('btn-add-user');
        const btnCancel = document.getElementById('btn-cancel');

        const userIdInput = document.getElementById('user-id');
        const userNameInput = document.getElementById('user-name');
        const userEmailInput = document.getElementById('user-email');
        const userRoleInput = document.getElementById('user-role');
        const userStatusInput = document.getElementById('user-status');

        let editingUserId = null;

        /* ===================== AQI CHART ===================== */
        const bufferLength = 20; // jumlah titik di chart
        const aqiChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                        type: 'line',
                        label: 'AQI Rata-rata',
                        data: [],
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'AQI Bar',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        yAxisID: 'y',
                        barPercentage: 0.7,
                        categoryPercentage: 0.5
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
                            text: 'Waktu'
                        },
                        stacked: false
                    }
                }
            }
        });

        // Inisialisasi chart dengan 0
        const initChartData = () => {
            const now = new Date();
            for (let i = bufferLength - 1; i >= 0; i--) {
                const timeLabel = new Date(now.getTime() - i * 10000).toLocaleTimeString();
                aqiChart.data.labels.push(timeLabel);
                aqiChart.data.datasets[0].data.push(0);
                aqiChart.data.datasets[1].data.push(0);
            }
            aqiChart.update();
        };


        const map = L.map('map').setView([-6.3238686, 107.3009286], 18);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        let markers = [];

        /* =========================== USER MANAGEMENT =========================== */
        const openUserModal = async (userId = null) => {
            editingUserId = null;
            userForm.reset();
            userRoleInput.value = 'mahasiswa';
            userStatusInput.value = '1';

            if (userId) {
                try {
                    const user = await fetchJSON(`/sistem_monitoring_udara/public/users/${userId}`);
                    if (!user) return;
                    editingUserId = user.id;
                    userIdInput.value = user.id;
                    userNameInput.value = user.name;
                    userEmailInput.value = user.email;
                    userRoleInput.value = user.role;
                    userStatusInput.value = user.is_active;
                } catch (err) {
                    console.error("Gagal load user:", err);
                }
            }
            document.getElementById('modal-title').innerText = editingUserId ? "Edit Pengguna" : "Tambah Pengguna";
            userModal.style.display = 'flex';
        };

        const closeUserModal = () => userModal.style.display = 'none';

        const renderUserTable = users => {
            tableBodyUser.innerHTML = '';
            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${capitalize(user.role)}</td>
            <td>${user.is_active == 1 ? 'Aktif' : 'Tidak Aktif'}</td>
            <td>
                <button class="btn btn-secondary btn-edit" data-id="${user.id}">Edit</button>
                <button class="btn btn-danger btn-delete" data-id="${user.id}">Hapus</button>
            </td>`;
                tableBodyUser.appendChild(row);
            });

            document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', e => openUserModal(e.target.dataset.id)));
            document.querySelectorAll('.btn-delete').forEach(btn => btn.addEventListener('click', e => deleteUser(e.target.dataset.id)));
        };

        const fetchUsers = async () => {
            const data = await fetchJSON('/sistem_monitoring_udara/public/users');
            if (data) renderUserTable(data);
        };

        const saveUser = async () => {
            const payload = {
                name: userNameInput.value,
                email: userEmailInput.value,
                role: userRoleInput.value,
                is_active: parseInt(userStatusInput.value)
            };
            const url = editingUserId ? `/sistem_monitoring_udara/public/users/${editingUserId}` : '/sistem_monitoring_udara/public/users';
            const method = editingUserId ? 'PUT' : 'POST';
            try {
                await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                closeUserModal();
                fetchUsers();
            } catch (err) {
                console.error("Gagal simpan user:", err);
            }
        };

        const deleteUser = async id => {
            if (!confirm("Yakin ingin menghapus user ini?")) return;
            try {
                await fetch(`/sistem_monitoring_udara/public/users/${id}`, {
                    method: 'DELETE'
                });
                fetchUsers();
            } catch (err) {
                console.error("Gagal hapus user:", err);
            }
        };

        /* =========================== EVENT LISTENERS =========================== */
        btnAddUser.addEventListener('click', () => openUserModal());
        btnCancel.addEventListener('click', closeUserModal);
        userForm.addEventListener('submit', e => {
            e.preventDefault();
            saveUser();
        });

        /* =========================== AQI CARD & DASHBOARD FUNCTIONS =========================== */
        const generateInsight = loc => {
            const {
                pm2_5,
                pm10
            } = loc.detail;
            const advice = getActivityAdvice(loc.aqi);
            return `Lokasi "${loc.name}" memiliki kualitas udara ${getAqiStatus(loc.aqi)}. 
    PM2.5: ${pm2_5} µg/m³, PM10: ${pm10} µg/m³.<br>
    <strong>Aktivitas aman:</strong> ${advice.ok}<br>
    <strong>Hindari:</strong> ${advice.avoid}`;
        };

        const updateLocationCards = async () => {
            const data = await fetchJSON('/sistem_monitoring_udara/public/avg-aqi');
            if (!data || !data.locations) return;
            cardsContainer.innerHTML = '';
            data.locations.forEach(loc => {
                const card = document.createElement('div');
                card.className = 'location-card';
                card.style.cssText = `border:1px solid #ccc; border-radius:10px; padding:15px; width:250px; background-color:rgba(255,255,255,0.05); box-shadow:0 2px 5px rgba(0,0,0,0.1);`;
                const statusColor = getAqiColor(loc.aqi);
                card.innerHTML = `<h3 style="margin-bottom:5px;">${loc.name}</h3>
            <p>Status AQI: <span style="color:${statusColor}; font-weight:bold;">${getAqiStatus(loc.aqi)}</span></p>
            <p>AQI: ${loc.aqi}</p>
            <p>PM2.5: ${loc.detail.pm2_5} µg/m³</p>
            <p>PM10: ${loc.detail.pm10} µg/m³</p>
            <p style="font-size:0.9em; color:#555;">${generateInsight(loc)}</p>`;
                cardsContainer.appendChild(card);
            });
            updateOverallSummary(data);
        };

        const updateOverallSummary = data => {
            if (!data || !data.locations) return;
            const avgAQI = data.average_aqi;
            const advice = getActivityAdvice(avgAQI);
            overallSummaryEl.innerHTML = `<div style="border:2px solid #444; border-radius:12px; padding:20px; background-color:rgba(255,255,255,0.05); box-shadow:0 3px 8px rgba(0,0,0,0.2);">
        <h2>Kesimpulan Keseluruhan</h2>
        <p>Status AQI Rata-rata: <strong>${getAqiStatus(avgAQI)}</strong> (AQI: ${avgAQI})</p>
        <p><strong>Aktivitas aman:</strong> ${advice.ok}</p>
        <p><strong>Hindari:</strong> ${advice.avoid}</p>
    </div>`;
        };

        const updateDashboard = async () => {
            const data = await fetchJSON('/sistem_monitoring_udara/public/avg-aqi');
            if (!data) return;

            const now = new Date().toLocaleTimeString();

            // Tambahkan label waktu
            aqiChart.data.labels.push(now);

            // Line chart: AQI rata-rata
            aqiChart.data.datasets[0].data.push(data.average_aqi ?? 0);

            // Bar chart: AQI maksimum dari semua lokasi (contoh)
            let maxAqi = 0;
            if (data.locations && data.locations.length > 0) {
                maxAqi = Math.max(...data.locations.map(loc => loc.aqi ?? 0));
            }
            aqiChart.data.datasets[1].data.push(maxAqi);

            // Batasi max 20 data
            if (aqiChart.data.labels.length > 20) {
                aqiChart.data.labels.shift();
                aqiChart.data.datasets.forEach(ds => ds.data.shift());
            }

            // Update chart
            aqiChart.update();

            // Update UI
            avgAqiEl.innerText = data.average_aqi ?? "-";
            aqiStatusEl.innerText = `Status AQI Rata-rata: ${getAqiStatus(data.average_aqi)}`;

            // Update map, table, dan cards
            updateMap(data.locations ?? []);
            updateTable(data.locations ?? []);
            updateLocationCards();
        };


        const updateMap = locations => {
            markers.forEach(m => map.removeLayer(m));
            markers = [];
            locations.forEach(loc => {
                const circle = L.circleMarker([parseFloat(loc.lat), parseFloat(loc.lon)], {
                    radius: 10,
                    fillColor: getAqiColor(loc.detail.pm2_5),
                    color: "#000",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(map);
                circle.bindPopup(`<strong>${loc.name}</strong><br>PM2.5: ${loc.detail.pm2_5}<br>PM10: ${loc.detail.pm10}`);
                markers.push(circle);
            });
        };

        const updateTable = locations => {
            tableBody.innerHTML = '';
            const now = new Date().toLocaleTimeString();
            locations.forEach(loc => {
                const row = document.createElement('tr');
                row.innerHTML = `<td>${loc.name}</td><td>${getAqiStatus(loc.detail.pm2_5)}</td><td>${loc.detail.pm2_5}</td><td>${now}</td>`;
                tableBody.appendChild(row);
            });
        };

        /* =========================== INITIALIZE =========================== */
        initChartData();
        fetchUsers();
        updateDashboard();
        setInterval(updateDashboard, 10000);

        const locationTableBody = document.getElementById('location-table-body');
        const locationModal = document.getElementById('location-modal');
        const locationForm = document.getElementById('location-form');
        const btnAddLocation = document.getElementById('btn-add-location');
        const btnCancelLocation = document.getElementById('location-cancel');
        const modalTitle = document.getElementById('location-modal-title');

        const locationIdInput = document.getElementById('location-id');
        const locationNameInput = document.getElementById('location-name');
        const locationLatInput = document.getElementById('location-lat');
        const locationLonInput = document.getElementById('location-lon');
        const locationAqiInput = document.getElementById('location-aqi');

        let editingLocationId = null;

        /* ===========================
           UTILS
        =========================== */
        const fetchJSONLocation = async (url, options = {}) => {
            try {
                const res = await fetch(url, options);
                return await res.json();
            } catch (err) {
                console.error(`Gagal fetch ${url}:`, err);
                return null;
            }
        };

        const openLocationModal = async (locationId = null) => {
            editingLocationId = null;
            locationForm.reset();

            if (locationId) {
                // Edit
                const location = await fetchJSONLocation(`/sistem_monitoring_udara/public/location/${locationId}`);
                if (!location) return alert('Lokasi tidak ditemukan');

                editingLocationId = location.id;
                locationIdInput.value = location.id;
                locationNameInput.value = location.name;
                locationLatInput.value = location.lat;
                locationLonInput.value = location.lon;

                modalTitle.innerText = "Edit Lokasi";
            } else {
                // Tambah
                modalTitle.innerText = "Tambah Lokasi";
            }

            locationModal.style.display = 'flex';
        };

        const closeLocationModal = () => {
            locationModal.style.display = 'none';
        };

        /* ===========================
           RENDER TABLE
        =========================== */
        const renderLocationTable = (locations) => {
            locationTableBody.innerHTML = '';
            locations.forEach(loc => {
                const row = document.createElement('tr');
                row.innerHTML = `
            <td>${loc.name}</td>
            <td>${loc.lat}</td>
            <td>${loc.lon}</td>
            <td>
                <button class="btn btn-secondary btn-edit" data-id="${loc.id}">Edit</button>
                <button class="btn btn-danger btn-delete" data-id="${loc.id}">Hapus</button>
            </td>
        `;
                locationTableBody.appendChild(row);
            });

            // Event listeners untuk edit & delete
            document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => {
                openLocationModal(btn.dataset.id);
            }));
            document.querySelectorAll('.btn-delete').forEach(btn => btn.addEventListener('click', () => {
                deleteLocation(btn.dataset.id);
            }));
        };

        /* ===========================
           FETCH LOCATIONS
        =========================== */
        const fetchLocations = async () => {
            const data = await fetchJSONLocation('/sistem_monitoring_udara/public/location');
            if (data) renderLocationTable(data);
        };

        /* ===========================
           SAVE LOCATION (POST/PUT)
        =========================== */
        const saveLocation = async (e) => {
            e.preventDefault();

            const payload = {
                name: locationNameInput.value,
                lat: parseFloat(locationLatInput.value),
                lon: parseFloat(locationLonInput.value),
            };

            const url = editingLocationId ? `/sistem_monitoring_udara/public/location/${editingLocationId}` : '/sistem_monitoring_udara/public/location';
            const method = editingLocationId ? 'PUT' : 'POST';

            const res = await fetchJSONLocation(url, {
                method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (res.error) return alert(res.error);

            closeLocationModal();
            fetchLocations();
        };

        /* ===========================
           DELETE LOCATION
        =========================== */
        const deleteLocation = async (id) => {
            if (!confirm("Yakin ingin menghapus lokasi ini?")) return;

            const res = await fetchJSONLocation(`/sistem_monitoring_udara/public/location/${id}`, {
                method: 'DELETE'
            });
            if (res.error) return alert(res.error);

            fetchLocations();
        };

        /* ===========================
           EVENT LISTENERS
        =========================== */
        btnAddLocation.addEventListener('click', () => openLocationModal());
        btnCancelLocation.addEventListener('click', closeLocationModal);
        locationForm.addEventListener('submit', saveLocation);

        /* ===========================
           INITIALIZE
        =========================== */
        fetchLocations();



        const AQI_STATUS = [{
                max: 50,
                text: "Baik",
                color: "#22c55e"
            },
            {
                max: 100,
                text: "Sedang",
                color: "#eab308"
            },
            {
                max: 150,
                text: "Tidak Sehat",
                color: "#f97316"
            },
            {
                max: 200,
                text: "Sangat Tidak Sehat",
                color: "#dc2626"
            },
            {
                max: 999,
                text: "Berbahaya",
                color: "#7f1d1d"
            }
        ];

        let chartData = Array.from({
            length: 10
        }, () => randomAQI());
        let history = [];

        function randomAQI() {
            return Math.floor(Math.random() * 200) + 20;
        }

        function randomInt(min, max) {
            return Math.floor(Math.random() * (max - min + 1) + min);
        }

        function updateStatusTitle(aqi) {
            let status = AQI_STATUS.find(s => aqi <= s.max);
            document.querySelector(".page-subtitle").innerHTML =
                `Kualitas Udara: <b style="color:${status.color}">${status.text}</b>`;
        }

        function notifyIfDanger(aqi) {
            if (aqi > 150) {
                const notif = document.querySelector(".badge");
                notif.style.display = "block";
                setTimeout(() => notif.style.display = "none", 5000);
                console.warn("⚠ Udara buruk – notifikasi muncul");
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const menuItems = document.querySelectorAll(".menu-item");
            const sections = document.querySelectorAll(".content-section");

            menuItems.forEach(item => {
                item.addEventListener("click", (e) => {
                    e.preventDefault();
                    const page = item.getAttribute("data-page");

                    menuItems.forEach(i => i.classList.remove("active"));
                    item.classList.add("active");

                    sections.forEach(s => s.classList.remove("active"));
                    document.getElementById(page).classList.add("active");

                });
            });

            document.getElementById("logoutBtn").addEventListener("click", () => {
                if (confirm('Apakah Anda yakin ingin keluar?')) {

                    fetch('/sistem_monitoring_udara/public/logout', {
                            method: 'GET',
                            credentials: 'include', // penting kalau pakai session/cookie
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => {
                            if (res.ok) {
                                alert('Anda telah logout. Terima kasih!');
                                window.location.href = '/sistem_monitoring_udara/public/login'; // redirect ke halaman login
                            } else {
                                alert('Gagal logout, coba lagi.');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Terjadi kesalahan saat logout.');
                        });

                }
            });
        });
    </script>
</body>

</html>