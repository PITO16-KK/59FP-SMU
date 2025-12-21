<?php
require_once __DIR__ . "/../../core/Database.php";

class LocationModel {
    private $conn;
    private $table = "location_data";

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // ============================
    // GET ALL
    // ============================
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================
    // GET BY ID
    // ============================
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================
    // CREATE
    // ============================
    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, lat, lon) VALUES (:name, :lat, :lon)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $data["name"]);
        $stmt->bindParam(":lat", $data["lat"]);
        $stmt->bindParam(":lon", $data["lon"]);

        return $stmt->execute();
    }

    // ============================
    // UPDATE
    // ============================
    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET name = :name, lat = :lat, lon = :lon WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $data["name"]);
        $stmt->bindParam(":lat", $data["lat"]);
        $stmt->bindParam(":lon", $data["lon"]);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ============================
    // DELETE
    // ============================
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
