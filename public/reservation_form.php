<?php
require_once '../db/Database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $query = "INSERT INTO reservations (customer_name, customer_email, room_id, check_in, check_out)
              VALUES (:name, :email, :room_id, :check_in, :check_out)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":room_id", $room_id);
    $stmt->bindParam(":check_in", $check_in);
    $stmt->bindParam(":check_out", $check_out);

    if ($stmt->execute()) {
        $success = "Reservation request submitted successfully.";
    } else {
        $error = "Failed to submit reservation.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Reservation</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
<div class="form-container">
    <h2>Book a Reservation</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= $_SESSION['user']['name']; ?>" required readonly>

        <label>Email:</label>
        <input type="email" name="email" value="<?= $_SESSION['user']['email']; ?>" required readonly>

        <label>Room ID:</label>
        <input type="text" name="room_id" required>

        <label>Check-In Date:</label>
        <input type="date" name="check_in" required>

        <label>Check-Out Date:</label>
        <input type="date" name="check_out" required>

        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
