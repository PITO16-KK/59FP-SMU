<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../mail.php";
require_once __DIR__ . "/../Models/UserModel.php";

class UserManagementController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Ambil semua user
    public function getAllUsers()
    {
        $users = $this->userModel->getAllUsers();
        $this->sendResponse($users);
    }

    public function getUserDetail($id)
    {
        // Pastikan $id berupa angka
        $id = (int)$id;

        $user = $this->userModel->getUserById($id);

        if ($user) {
            $this->sendResponse($user);
        } else {
            $this->sendResponse(['error' => 'User tidak ditemukan'], 404);
        }
    }

    // Tambah user baru
    public function createUser()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['name'], $data['email'], $data['role'], $data['is_active'])) {
            return $this->sendResponse(['error' => 'Data tidak lengkap'], 400);
        }

        // Password default (misal: 123456), bisa dikirim atau generate random
        $password = password_hash('123456', PASSWORD_DEFAULT);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $password,
            'role' => $data['role'],
            'is_active' => 1,
            'activation_code' => null
        ];


        $insertedId = $this->userModel->createUser($userData);

        if ($insertedId) {
            // Kirim email notifikasi jika mau
            // sendEmail($data['email'], "Akun Anda Dibuat", "Password: 123456");

            $this->sendResponse(['message' => 'User berhasil ditambahkan', 'id' => $insertedId]);
        } else {
            $this->sendResponse(['error' => 'Gagal menambahkan user'], 500);
        }
    }

    // Update user
    public function updateUser($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['name'], $data['email'], $data['role'], $data['is_active'])) {
            return $this->sendResponse(['error' => 'Data tidak lengkap'], 400);
        }

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $data['is_active']
        ];

        $updated = $this->userModel->updateUser($id, $userData);

        if ($updated) {
            $this->sendResponse(['message' => 'User berhasil diperbarui']);
        } else {
            $this->sendResponse(['error' => 'Gagal memperbarui user'], 500);
        }
    }

    // Hapus user
    public function deleteUser($id)
    {
        $deleted = $this->userModel->deleteUser($id);

        if ($deleted) {
            $this->sendResponse(['message' => 'User berhasil dihapus']);
        } else {
            $this->sendResponse(['error' => 'Gagal menghapus user'], 500);
        }
    }

    // Fungsi response JSON
    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
