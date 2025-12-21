<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../mail.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/LocationModel.php";
require_once __DIR__ . "/../models/MotivasiModel.php";
require_once __DIR__ . "/../models/CityFavoritesModel.php";

class DashboardController extends BaseController
{
    // Tampilkan form register
    public function dashboard()
    {
        // Pastikan user login
        if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_role'])) {
            header("Location: /sistem_monitoring_udara/public/login");
            exit;
        }

        $role = $_SESSION['user_role'];
        $id = $_SESSION['user_id'];

        // Ambil data
        $locationModel = new LocationModel();
        $locations = $locationModel->getAll();

        $CityFavoritesModel = new CityFavoritesModel();
        $city_favorites = $CityFavoritesModel->getByUserId($id);

        $motivasiModel = new MotivasiModel();
        $motivasi = $motivasiModel->getAll();

        $userModel = new UserModel();
        $totalUsers = $userModel->getCount();
        $totalLocations = count($locations);

        // Kumpulkan semua data ke array tunggal
        $data = [
            "locations" => $locations,
            "totalUsers" => $totalUsers,
            "totalLocations" => $totalLocations,
            "motivasi" => $motivasi,
            "city_favorites" => $city_favorites
        ];

        // Tampilkan view sesuai role
        switch ($role) {
            case "admin":
                $this->view("dashboard/admin/index", $data);
                break;

            case "mahasiswa":
                $this->view("dashboard/mahasiswa/index", $data);
                break;

            case "dosen":
                $this->view("dashboard/dosen/index", $data);
                break;

            default:
                header("Location: /sistem_monitoring_udara/public/login");
                exit;
        }
    }
}
