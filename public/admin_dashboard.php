<?php
require_once '../db/Database.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Handle Approve/Cancel actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $updateQuery = "UPDATE reservations SET status = 'Approved' WHERE id = :id";
    } elseif ($action === 'cancel') {
        $updateQuery = "UPDATE reservations SET status = 'Cancelled' WHERE id = :id";
    }
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(":id", $reservation_id);
    $stmt->execute();
}

// Fetch Reservations
$query = "SELECT r.id, u.name, u.email, r.room_id, r.status FROM reservations r JOIN users u ON r.user_id = u.id ORDER BY r.id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Reservations</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>
<body>
<div class="dashboard-container">
    <h2>Manage Reservations</h2>
    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Room ID</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= $res['id']; ?></td>
                <td><?= $res['name']; ?></td>
                <td><?= $res['email']; ?></td>
                <td><?= $res['room_id']; ?></td>
                <td><?= $res['status']; ?></td>
                <td>
                    <?php if ($res['status'] === 'Pending'): ?>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="reservation_id" value="<?= $res['id']; ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit">Approve</button>
                        </form>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="reservation_id" value="<?= $res['id']; ?>">
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit">Cancel</button>
                        </form>
                    <?php else: ?>
                        <?= $res['status']; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>
</body>
</html>
