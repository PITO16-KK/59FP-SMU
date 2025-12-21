<?php
class UserModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function register($data)
    {
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $role = 'mahasiswa';

        if (!$name || !$email || !$password) return ['status' => false, 'message' => 'Field kosong'];

        // Cek email
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) return ['status' => false, 'message' => 'Email sudah terdaftar'];

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $code = bin2hex(random_bytes(16));
        $stmt = $this->conn->prepare("INSERT INTO users (name,email,password,role,activation_code) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $email, $hash, $role, $code]);

        if (sendActivationEmail($email, $code)) {
            return ['status' => true, 'message' => 'Cek email untuk aktivasi'];
        } else {
            return ['status' => false, 'message' => 'Gagal kirim email'];
        }
    }

    public function login($data)
    {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 0)
                return ['status' => false, 'message' => 'Akun belum aktif'];

            $token = base64_encode($user['email'] . '|' . time());

            return [
                'status' => true,
                'token'  => $token,
                'id'     => $user['id'],
                'name'   => $user['name'],
                'email'  => $user['email'],
                'role'   => $user['role']
            ];
        }

        return ['status' => false, 'message' => 'Email atau password salah'];
    }


    public function activate($code)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE activation_code=? AND is_active=0");
        $stmt->execute([$code]);
        if ($stmt->rowCount() == 1) {
            $stmt = $this->conn->prepare("UPDATE users SET is_active=1, activation_code=NULL WHERE activation_code=?");
            $stmt->execute([$code]);
            return "Akun berhasil diaktifkan!";
        }
        return "Kode tidak valid atau akun sudah aktif";
    }

    public function getCount()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // Ambil semua user
    public function getAllUsers()
    {
        $stmt = $this->conn->prepare("SELECT id, name, email, role, is_active FROM users ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT id, name, email, role, is_active FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Tambah user baru
    public function createUser($data)
    {
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $role = $data['role'] ?? 'user';
        $status = isset($data['is_active']) && $data['is_active'] ? 1 : 0;

        // Default password random
        $password = password_hash($data['password'] ?? '123456', PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO users (name,email,password,role,is_active) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $email, $password, $role, $status]);

        return $this->conn->lastInsertId();
    }

    // Update user
    public function updateUser($id, $data)
    {
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $role = $data['role'] ?? 'user';
        $status = isset($data['is_active']) && $data['is_active'] ? 1 : 0;

        $stmt = $this->conn->prepare("UPDATE users SET name=?, email=?, role=?, is_active=? WHERE id=?");
        return $stmt->execute([$name, $email, $role, $status, $id]);
    }

    public function updatePassword($id, $hashedPass)
    {
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE id=?");
        return $stmt->execute([$hashedPass, $id]);
    }


    // Hapus user
    public function deleteUser($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }
}
