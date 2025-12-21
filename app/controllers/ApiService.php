<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../mail.php";
require_once __DIR__ . "/../Models/LocationModel.php";

class ApiService extends BaseController
{
    public function getEnv($key, $default = null)
    {
        $env = @parse_ini_file(__DIR__ . '/../../.env');
        return $env[$key] ?? $default;
    }

    public function getAirQualityOpenWeather()
    {
        header('Content-Type: application/json');

        $apiKey = $this->getEnv("API_KEY");

        if (!$apiKey) {
            echo json_encode(['error' => 'API Key tidak ditemukan']);
            exit;
        }

        $lat = $_GET['lat'] ?? null;
        $lon = $_GET['lon'] ?? null;

        if (!$lat || !$lon) {
            echo json_encode(['error' => 'Latitude dan Longitude wajib dikirim']);
            exit;
        }

        // API Air Pollution
        $urlPollution = "https://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";

        $ch = curl_init($urlPollution);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responsePollution = curl_exec($ch);
        curl_close($ch);

        if (!$responsePollution) {
            echo json_encode(['error' => 'Gagal mengambil data polusi udara']);
            exit;
        }

        $dataPollution = json_decode($responsePollution, true);
        $list = $dataPollution['list'][0] ?? [];

        $aqi = $list['main']['aqi'] ?? null;
        if ($aqi === null) {
            $status = 'Tidak diketahui';
        } elseif ($aqi >= 0 && $aqi <= 50) {
            $status = 'Baik';
        } elseif ($aqi <= 100) {
            $status = 'Sedang';
        } elseif ($aqi <= 150) {
            $status = 'Tidak Sehat untuk kelompok sensitif';
        } elseif ($aqi <= 200) {
            $status = 'Buruk';
        } elseif ($aqi > 200) {
            $status = 'Sangat Buruk';
        } else {
            $status = 'Tidak diketahui';
        }
        $components = $list['components'] ?? [];

        // API Weather (untuk suhu & kelembaban)
        $urlWeather = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

        $ch2 = curl_init($urlWeather);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $responseWeather = curl_exec($ch2);
        curl_close($ch2);

        if (!$responseWeather) {
            echo json_encode(['error' => 'Gagal mengambil data cuaca']);
            exit;
        }

        $dataWeather = json_decode($responseWeather, true);

        $temperature = $dataWeather['main']['temp'] ?? null;
        $humidity = $dataWeather['main']['humidity'] ?? null;

        echo json_encode([
            'coord'       => $dataPollution['coord'],
            'aqi'         => $aqi,
            'status'      => $status,
            'components'  => $components,
            'temperature' => $temperature,
            'humidity'    => $humidity
        ]);

        exit;
    }

    public function getAirQualityByCity()
    {
        header('Content-Type: application/json');

        $apiKey = $this->getEnv("API_KEY");

        if (!$apiKey) {
            echo json_encode(['error' => 'API Key tidak ditemukan']);
            exit;
        }

        $city = $_GET['city'] ?? null;

        if (!$city) {
            echo json_encode(['error' => 'Nama kota wajib dikirim']);
            exit;
        }

        // API Weather untuk dapat koordinat kota
        $urlGeo = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}";

        $chGeo = curl_init($urlGeo);
        curl_setopt($chGeo, CURLOPT_RETURNTRANSFER, true);
        $responseGeo = curl_exec($chGeo);
        curl_close($chGeo);

        if (!$responseGeo) {
            echo json_encode(['error' => 'Gagal mengambil data koordinat kota']);
            exit;
        }

        $dataGeo = json_decode($responseGeo, true);

        if (!isset($dataGeo['coord']['lat']) || !isset($dataGeo['coord']['lon'])) {
            echo json_encode(['error' => 'Kota tidak ditemukan']);
            exit;
        }

        $lat = $dataGeo['coord']['lat'];
        $lon = $dataGeo['coord']['lon'];

        // API Air Pollution
        $urlPollution = "https://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";

        $chPollution = curl_init($urlPollution);
        curl_setopt($chPollution, CURLOPT_RETURNTRANSFER, true);
        $responsePollution = curl_exec($chPollution);
        curl_close($chPollution);

        if (!$responsePollution) {
            echo json_encode(['error' => 'Gagal mengambil data polusi udara']);
            exit;
        }

        $dataPollution = json_decode($responsePollution, true);
        $list = $dataPollution['list'][0] ?? [];

        $aqiMap = [
            1 => 'Baik',
            2 => 'Sedang',
            3 => 'Tidak Sehat untuk kelompok sensitif',
            4 => 'Buruk',
            5 => 'Sangat Buruk'
        ];

        $aqi = $list['main']['aqi'] ?? null;
        $status = $aqiMap[$aqi] ?? 'Tidak diketahui';
        $components = $list['components'] ?? [];

        // API Weather untuk suhu & kelembaban
        $urlWeather = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

        $chWeather = curl_init($urlWeather);
        curl_setopt($chWeather, CURLOPT_RETURNTRANSFER, true);
        $responseWeather = curl_exec($chWeather);
        curl_close($chWeather);

        if (!$responseWeather) {
            echo json_encode(['error' => 'Gagal mengambil data cuaca']);
            exit;
        }

        $dataWeather = json_decode($responseWeather, true);

        $temperature = $dataWeather['main']['temp'] ?? null;
        $humidity = $dataWeather['main']['humidity'] ?? null;

        echo json_encode([
            'city'        => $city,
            'coord'       => $dataPollution['coord'] ?? ['lat' => $lat, 'lon' => $lon],
            'aqi'         => $aqi,
            'status'      => $status,
            'components'  => $components,
            'temperature' => $temperature,
            'humidity'    => $humidity
        ]);

        exit;
    }

    public function getAverageAQI()
    {
        header('Content-Type: application/json');

        $apiKey = $this->getEnv("API_KEY");
        if (!$apiKey) {
            echo json_encode(['error' => 'API Key tidak ditemukan']);
            exit;
        }

        // Ambil semua lokasi
        $locationModel = new LocationModel();
        $locations = $locationModel->getAll();

        if (empty($locations)) {
            echo json_encode(['error' => 'Tidak ada data lokasi']);
            exit;
        }

        $totalAqi = 0;
        $count = 0;
        $details = []; // Bisa simpan tiap lokasi juga

        foreach ($locations as $loc) {
            $lat = $loc['lat'];
            $lon = $loc['lon'];

            $urlPollution = "https://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
            $ch = curl_init($urlPollution);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responsePollution = curl_exec($ch);
            curl_close($ch);

            if ($responsePollution) {
                $dataPollution = json_decode($responsePollution, true);
                $aqi = $dataPollution['list'][0]['main']['aqi'] ?? null;
                if ($aqi !== null) {
                    $totalAqi += $aqi;
                    $count++;

                    // Simpan data tiap lokasi (opsional)
                    $details[] = [
                        'name' => $loc['name'],
                        'lat'  => $lat,
                        'lon'  => $lon,
                        'aqi'  => $aqi,
                        'detail'  => $dataPollution['list'][0]["components"]
                    ];
                }
            }
        }

        $avgAqi = $count > 0 ? round($totalAqi / $count, 2) : null;

        echo json_encode([
            'average_aqi' => $avgAqi,
            'locations'   => $details
        ]);
        exit;
    }

    public function getAirQualityAllLocation()
    {
        header('Content-Type: application/json');

        $apiKey = $this->getEnv("API_KEY");
        if (!$apiKey) {
            echo json_encode(['error' => 'API Key tidak ditemukan']);
            exit;
        }

        $locationModel = new LocationModel();
        $locations = $locationModel->getAll();

        if (empty($locations)) {
            echo json_encode(['error' => 'Tidak ada data lokasi']);
            exit;
        }

        $results = [];

        $aqiMap = [
            1 => 'Baik',
            2 => 'Sedang',
            3 => 'Tidak Sehat untuk kelompok sensitif',
            4 => 'Buruk',
            5 => 'Sangat Buruk'
        ];

        // VARIABLE UNTUK AVERAGE
        $sumAqi = 0;
        $sumTemp = 0;
        $sumHumidity = 0;
        $count = 0;

        foreach ($locations as $loc) {
            $lat = $loc['lat'];
            $lon = $loc['lon'];

            // ===== POLLUTION API =====
            $urlPollution = "https://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
            $ch = curl_init($urlPollution);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responsePollution = curl_exec($ch);
            curl_close($ch);

            $aqi = null;
            $status = null;
            $components = [];

            if ($responsePollution) {
                $dataPollution = json_decode($responsePollution, true);
                $aqi = $dataPollution['list'][0]['main']['aqi'] ?? null;
                $status = $aqiMap[$aqi] ?? "Tidak diketahui";
                $components = $dataPollution['list'][0]['components'] ?? [];
            }

            // ===== WEATHER API =====
            $urlWeather = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";
            $ch2 = curl_init($urlWeather);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            $responseWeather = curl_exec($ch2);
            curl_close($ch2);

            $temperature = null;
            $humidity = null;

            if ($responseWeather) {
                $dataWeather = json_decode($responseWeather, true);
                $temperature = $dataWeather['main']['temp'] ?? null;
                $humidity = $dataWeather['main']['humidity'] ?? null;
            }

            // hitung sum untuk rata-rata jika nilai valid
            if ($aqi !== null) $sumAqi += $aqi;
            if ($temperature !== null) $sumTemp += $temperature;
            if ($humidity !== null) $sumHumidity += $humidity;
            $count++;

            // push ke result
            $results[] = [
                'id'          => $loc['id'],
                'name'        => $loc['name'],
                'lat'         => $lat,
                'lon'         => $lon,
                'aqi'         => $aqi,
                'status'      => $status,
                'components'  => $components,
                'temperature' => $temperature,
                'humidity'    => $humidity
            ];
        }

        // AVERAGE calculation
        $avgAqi = $count > 0 ? round($sumAqi / $count, 2) : null;
        $avgTemp = $count > 0 ? round($sumTemp / $count, 2) : null;
        $avgHumidity = $count > 0 ? round($sumHumidity / $count, 2) : null;

        echo json_encode([
            'count' => count($results),
            'average' => [
                'aqi' => $avgAqi,
                'temperature' => $avgTemp,
                'humidity' => $avgHumidity
            ],
            'locations' => $results
        ]);
        exit;
    }
    public function sendNotifAir()
    {
        header('Content-Type: application/json');

        $apiKey = $this->getEnv("API_KEY");
        if (!$apiKey) {
            echo json_encode(['error' => 'API Key tidak ditemukan']);
            exit;
        }

        $role = $_SESSION['user_email'];

        $locationModel = new LocationModel();
        $locations = $locationModel->getAll();

        if (empty($locations)) {
            echo json_encode(['error' => 'Tidak ada data lokasi']);
            exit;
        }

        $results = [];

        $aqiMap = [
            1 => 'Baik',
            2 => 'Sedang',
            3 => 'Tidak Sehat untuk kelompok sensitif',
            4 => 'Buruk',
            5 => 'Sangat Buruk'
        ];

        // VARIABLE UNTUK AVERAGE
        $sumAqi = 0;
        $sumTemp = 0;
        $sumHumidity = 0;
        $count = 0;

        foreach ($locations as $loc) {
            $lat = $loc['lat'];
            $lon = $loc['lon'];

            // ===== POLLUTION API =====
            $urlPollution = "https://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
            $ch = curl_init($urlPollution);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responsePollution = curl_exec($ch);
            curl_close($ch);

            $aqi = null;
            $status = null;
            $components = [];

            if ($responsePollution) {
                $dataPollution = json_decode($responsePollution, true);
                $aqi = $dataPollution['list'][0]['main']['aqi'] ?? null;
                $status = $aqiMap[$aqi] ?? "Tidak diketahui";
                $components = $dataPollution['list'][0]['components'] ?? [];
            }

            // ===== WEATHER API =====
            $urlWeather = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";
            $ch2 = curl_init($urlWeather);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            $responseWeather = curl_exec($ch2);
            curl_close($ch2);

            $temperature = null;
            $humidity = null;

            if ($responseWeather) {
                $dataWeather = json_decode($responseWeather, true);
                $temperature = $dataWeather['main']['temp'] ?? null;
                $humidity = $dataWeather['main']['humidity'] ?? null;
            }

            // HITUNG SUM
            if ($aqi !== null) $sumAqi += $aqi;
            if ($temperature !== null) $sumTemp += $temperature;
            if ($humidity !== null) $sumHumidity += $humidity;
            $count++;

            // ====== KIRIM NOTIFIKASI JIKA AQI BERBAHAYA ======
            if ($aqi >= 4) {
                $isSent = sendNotif($role, $aqi, $status, $loc['name']);

                if (!$isSent) {
                    echo json_encode([
                        'error' => "Gagal mengirim email notifikasi untuk lokasi {$loc['name']}"
                    ]);
                    exit;
                }
            }


            // push ke result
            $results[] = [
                'id'          => $loc['id'],
                'name'        => $loc['name'],
                'lat'         => $lat,
                'lon'         => $lon,
                'aqi'         => $aqi,
                'status'      => $status,
                'components'  => $components,
                'temperature' => $temperature,
                'humidity'    => $humidity
            ];
        }

        // ========== RESPONSE SUCCESS EMAIL SENT ==========
        echo json_encode([
            "message" => "Email berhasil terkirim",
            "locations" => $results
        ]);
        exit;
    }
}
