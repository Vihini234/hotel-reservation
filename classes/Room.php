<?php
require_once '../db/Database.php';

class Room {
    private $conn;
    private $table_name = "rooms";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllRooms() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function addRoom($room_number, $room_type, $price) {
        $query = "INSERT INTO " . $this->table_name . " (room_number, room_type, price) VALUES (:room_number, :room_type, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":room_number", $room_number);
        $stmt->bindParam(":room_type", $room_type);
        $stmt->bindParam(":price", $price);
        return $stmt->execute();
    }

    public function updateRoom($id, $room_number, $room_type, $price, $status) {
        $query = "UPDATE " . $this->table_name . " SET room_number = :room_number, room_type = :room_type, price = :price, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":room_number", $room_number);
        $stmt->bindParam(":room_type", $room_type);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function deleteRoom($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
