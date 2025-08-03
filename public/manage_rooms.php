<?php
session_start();
include 'navbar.php';
// Page Content Here

require_once '../classes/Room.php';
$room = new Room();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $room->addRoom($_POST['room_number'], $_POST['room_type'], $_POST['price']);
    } elseif (isset($_POST['update'])) {
        $room->updateRoom($_POST['id'], $_POST['room_number'], $_POST['room_type'], $_POST['price'], $_POST['status']);
    } elseif (isset($_POST['delete'])) {
        $room->deleteRoom($_POST['id']);
    }
}

$rooms = $room->getAllRooms();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Rooms</title>
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
        <h2>Add New Room</h2>
        <form method="POST">
            Room Number: <input type="text" name="room_number" required>
            Room Type: <input type="text" name="room_type" required>
            Price: <input type="number" step="0.01" name="price" required>
            <button type="submit" name="add">Add Room</button>
        </form>

        <h2>Manage Existing Rooms</h2>
        <table>
            <tr>
                <th>Room Number</th>
                <th>Type</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $rooms->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <form method="POST">
                        <td><input type="text" name="room_number" value="<?= $row['room_number'] ?>"></td>
                        <td><input type="text" name="room_type" value="<?= $row['room_type'] ?>"></td>
                        <td><input type="number" step="0.01" name="price" value="<?= $row['price'] ?>"></td>
                        <td>
                            <select name="status">
                                <option value="Available" <?= $row['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                                <option value="Booked" <?= $row['status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="update">Update</button>
                            <button type="submit" name="delete">Delete</button>
                        </td>
                    </form>
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
