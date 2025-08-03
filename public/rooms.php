<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once '../classes/Room.php';
$room = new Room();
$rooms = $room->getAllRooms();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Rooms</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Hotel Reservation System</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="rooms.php">Rooms</a>
            <a href="reservation_form.php">Reservation</a>
            <a href="manage_rooms.php">Manage Rooms</a>
        </nav>
    </header>

    <main>
        <h2>Available Rooms</h2>
        <table>
            <tr>
                <th>Room Number</th>
                <th>Type</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $rooms->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
                <td><?= $row['room_number'] ?></td>
                <td><?= $row['room_type'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['status'] ?></td>
            </tr>
            <?php } ?>
        </table>
    </main>

    <footer>
        <p>&copy; 2025 Hotel Reservation System</p>
    </footer>
</div>
</body>
</html>
