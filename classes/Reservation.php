<?php
require_once '../db/Database.php';

class Reservation {
    private $conn;
    private $table_name = "reservations";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function makeReservation($customer_name, $customer_email, $room_id, $check_in, $check_out) {
        $query = "INSERT INTO " . $this->table_name . " (customer_name, customer_email, room_id, check_in, check_out)
                  VALUES (:customer_name, :customer_email, :room_id, :check_in, :check_out)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_name", $customer_name);
        $stmt->bindParam(":customer_email", $customer_email);
        $stmt->bindParam(":room_id", $room_id);
        $stmt->bindParam(":check_in", $check_in);
        $stmt->bindParam(":check_out", $check_out);

        if ($stmt->execute()) {
            $updateQuery = "UPDATE rooms SET status = 'Booked' WHERE id = :room_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":room_id", $room_id);
            $updateStmt->execute();
            return true;
        }
        return false;
    }
}
?>
