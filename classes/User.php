<?php
require_once '../db/Database.php';

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($name, $email, $password) {
        // Check if email already exists
        $checkQuery = "SELECT id FROM users WHERE email = :email";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":email", $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            return false; // Email already exists
        }

        // Proceed to register
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'customer')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);
        return $stmt->execute();
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
