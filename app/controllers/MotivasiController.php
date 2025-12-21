<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../Models/MotivasiModel.php";

class MotivasiController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new MotivasiModel();
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ===================== READ =====================
    public function getAllMotivasi()
    {
        $motivasi = $this->model->getAll();
        $this->sendResponse($motivasi);
    }

    public function getDetailMotivasi($id)
    {
        $motivasi = $this->model->getById($id);
        if ($motivasi) {
            $this->sendResponse($motivasi);
        } else {
            $this->sendResponse(['error' => 'motivasi tidak ditemukan'], 404);
        }
    }

    // ===================== CREATE =====================
    public function createMotivasi()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['kutipan'], $data['penulis'])) {
            return $this->sendResponse(['error' => 'Input tidak valid'], 400);
        }

        $newMotivasi = [
            'kutipan' => $data['kutipan'],
            'penulis' => $data['penulis'],
        ];

        $id = $this->model->create($newMotivasi);
        if ($id) {
            $this->sendResponse(array_merge(['id' => $id], $newMotivasi), 201);
        } else {
            $this->sendResponse(['error' => 'Gagal menambahkan lokasi'], 500);
        }
    }

    // ===================== UPDATE =====================
    public function updateMotivasi($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $motivasi = $this->model->getById($id);

        if (!$motivasi) {
            return $this->sendResponse(['error' => 'motivasi tidak ditemukan'], 404);
        }

        $updatedmotivasi = [
            'kutipan' => $data['kutipan'] ?? $motivasi['kutipan'],
            'penulis' => $data['penulis'] ?? $motivasi['penulis'],
        ];

        $success = $this->model->update($id, $updatedmotivasi);
        if ($success) {
            $this->sendResponse(array_merge(['id' => $id], $updatedmotivasi));
        } else {
            $this->sendResponse(['error' => 'Gagal memperbarui lokasi'], 500);
        }
    }

    // ===================== DELETE =====================
    public function deleteMotivasi($id)
    {
        $motivasi = $this->model->getById($id);
        if (!$motivasi) {
            return $this->sendResponse(['error' => 'motivasi tidak ditemukan'], 404);
        }

        $success = $this->model->delete($id);
        if ($success) {
            $this->sendResponse(['message' => 'Lokasi berhasil dihapus']);
        } else {
            $this->sendResponse(['error' => 'Gagal menghapus lokasi'], 500);
        }
    }
}
