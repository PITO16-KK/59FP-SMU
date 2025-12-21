<?php
require_once __DIR__ . "/../../core/Database.php";

class CityFavoritesModel {
    private $conn;
    private $table = "location_favorites";

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // ============================
    // GET ALL FAVORITES
    // ============================
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================
    // GET FAVORITES BY ID
    // ============================
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================
    // GET FAVORITES BY USER ID
    // ============================
    public function getByUserId($userId) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================
    // CREATE FAVORITE
    // ============================
    public function create($data) {
        $query = "INSERT INTO {$this->table} (user_id, city, latitude, longitude) 
                  VALUES (:user_id, :city, :latitude, :longitude)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $data["user_id"], PDO::PARAM_INT);
        $stmt->bindParam(":city", $data["city"]);
        $stmt->bindParam(":latitude", $data["latitude"]);
        $stmt->bindParam(":longitude", $data["longitude"]);

        return $stmt->execute();
    }

    // ============================
    // UPDATE FAVORITE
    // ============================
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET city = :city, latitude = :latitude, longitude = :longitude 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":city", $data["city"]);
        $stmt->bindParam(":latitude", $data["latitude"]);
        $stmt->bindParam(":longitude", $data["longitude"]);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ============================
    // DELETE FAVORITE
    // ============================
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
