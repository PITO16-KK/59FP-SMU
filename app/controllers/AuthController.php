<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../mail.php";
require_once __DIR__ . "/../Models/UserModel.php";

class AuthController extends BaseController
{

    // Proses form register
    public function registerAction()
    {
        $user = new UserModel();
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => 'mahasiswa'
        ];
        $result = $user->register($data);
        $message = $result['message'];
        $this->view('login', ['message' => $message]);
    }

    // Tampilkan form login
    public function loginForm()
    {
        $this->view('login');
    }

    // Proses form login
    public function loginAction()
    {
        $user = new UserModel();
        $data = [
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];
        $result = $user->login($data);

        if ($result['status']) {

            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_role'] = $result['role'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $data['email'];


            header("Location: /sistem_monitoring_udara/public/dashboard");
            exit;
        } else {
            $message = $result['message'];
            $this->view('login', ['message' => $message]);
        }
    }

    public function logout()
    {
        session_start();
        $_SESSION = [];
        session_unset();
        session_destroy();

        // Hapus cookie session biar browser tidak kirim cookie kadaluarsa
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        echo json_encode(['status' => true]);
        exit;
    }


    public function activate()
    {
        $code = $_GET['code'] ?? '';
        $user = new UserModel();
        $msg = $user->activate($code);
        $this->view('activate', ['message' => $msg]);
    }

    public function editProfile()
    {
        header("Content-Type: application/json");

        $input = json_decode(file_get_contents("php://input"), true);

        $name = !empty($input['name']) ? $input['name'] : ($_SESSION['user_name'] ?? null);
        $email = !empty($input['email']) ? $input['email'] : ($_SESSION['user_email'] ?? null);
        $password = $input['password'] ?? null;

        if (!$name || !$email) {
            echo json_encode(["status" => false, "message" => "Nama dan email wajib diisi"]);
            return;
        }

        $userModel = new UserModel();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(["status" => false, "message" => "User tidak ditemukan"]);
            return;
        }

        // Data umum update
        $dataToUpdate = [
            "name" => $name,
            "email" => $email,
            "role" => $_SESSION["user_role"],
            "is_active" => 1
        ];

        // Update data dasar
        $update = $userModel->updateUser($userId, $dataToUpdate);

        // Jika ada password baru, update juga
        if (!empty($password)) {
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $userModel->updatePassword($userId, $hashedPass);
        }

        if ($update) {
            $_SESSION["user_name"] = $name;
            $_SESSION["user_email"] = $email;

            echo json_encode(["status" => true, "message" => "Profil berhasil diperbarui"]);
        } else {
            echo json_encode(["status" => false, "message" => "Gagal memperbarui profil"]);
        }
    }
}
