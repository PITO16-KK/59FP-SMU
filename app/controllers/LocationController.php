<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/../Models/LocationModel.php";
require_once __DIR__ . "/../Models/CityFavoritesModel.php";

class LocationController extends BaseController
{
    private $model;
    private $CityFavoritesModel;

    public function __construct()
    {
        $this->model = new LocationModel();
        $this->CityFavoritesModel = new CityFavoritesModel();
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ===================== READ =====================
    public function getAllLocation()
    {
        $locations = $this->model->getAll();
        $this->sendResponse($locations);
    }

    public function getDetailLocation($id)
    {
        $location = $this->model->getById($id);
        if ($location) {
            $this->sendResponse($location);
        } else {
            $this->sendResponse(['error' => 'Location tidak ditemukan'], 404);
        }
    }

    // ===================== CREATE =====================
    public function createLocation()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'], $data['lat'], $data['lon'])) {
            return $this->sendResponse(['error' => 'Input tidak valid'], 400);
        }

        $newLocation = [
            'name' => $data['name'],
            'lat' => $data['lat'],
            'lon' => $data['lon'],
        ];

        $id = $this->model->create($newLocation);
        if ($id) {
            $this->sendResponse(array_merge(['id' => $id], $newLocation), 201);
        } else {
            $this->sendResponse(['error' => 'Gagal menambahkan lokasi'], 500);
        }
    }

    // ===================== UPDATE =====================
    public function updateLocation($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $location = $this->model->getById($id);

        if (!$location) {
            return $this->sendResponse(['error' => 'Location tidak ditemukan'], 404);
        }

        $updatedLocation = [
            'name' => $data['name'] ?? $location['name'],
            'lat' => $data['lat'] ?? $location['lat'],
            'lon' => $data['lon'] ?? $location['lon'],
        ];

        $success = $this->model->update($id, $updatedLocation);
        if ($success) {
            $this->sendResponse(array_merge(['id' => $id], $updatedLocation));
        } else {
            $this->sendResponse(['error' => 'Gagal memperbarui lokasi'], 500);
        }
    }

    // ===================== DELETE =====================
    public function deleteLocation($id)
    {
        $location = $this->model->getById($id);
        if (!$location) {
            return $this->sendResponse(['error' => 'Location tidak ditemukan'], 404);
        }

        $success = $this->model->delete($id);
        if ($success) {
            $this->sendResponse(['message' => 'Lokasi berhasil dihapus']);
        } else {
            $this->sendResponse(['error' => 'Gagal menghapus lokasi'], 500);
        }
    }

    public function createLocationFavorite()
    {
        header('Content-Type: application/json'); // <- ini penting
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['city'], $data['latitude'], $data['longitude'])) {
            echo json_encode(['error' => 'Input tidak valid']);
            http_response_code(400);
            exit;
        }

        $newLocation = [
            'user_id' => $data['user_id'],
            'city' => $data['city'],  // sebelumnya $data['name'], harus sesuai payload
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ];

        $id = $this->CityFavoritesModel->create($newLocation);
        if ($id) {
            echo json_encode(array_merge(['id' => $id], $newLocation));
            http_response_code(201);
            exit;
        } else {
            echo json_encode(['error' => 'Gagal menambahkan lokasi']);
            http_response_code(500);
            exit;
        }
    }

    public function deleteLocationFavorite($id)
    {
        header('Content-Type: application/json'); // <- ini penting
        $location = $this->CityFavoritesModel->getById($id);
        if (!$location) {
            echo json_encode(['error' => 'Location tidak ditemukan']);
            http_response_code(404);
            exit;
        }

        $success = $this->CityFavoritesModel->delete($id);
        if ($success) {
            echo json_encode(['message' => 'Lokasi Favorit berhasil dihapus']);
            exit;
        } else {
            echo json_encode(['error' => 'Gagal menghapus lokasi']);
            http_response_code(500);
            exit;
        }
    }
}
