<?php
require_once '../db/Database.php';
require_once '../classes/User.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Handle various actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'approve':
        case 'cancel':
            $reservation_id = $_POST['reservation_id'];
            $status = ($action === 'approve') ? 'Approved' : 'Cancelled';
            $updateQuery = "UPDATE reservations SET status = :status WHERE id = :id";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $reservation_id);
            $stmt->execute();
            break;
            
        case 'delete_user':
            $user_id = $_POST['user_id'];
            $deleteQuery = "DELETE FROM users WHERE id = :id AND role != 'admin'";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bindParam(":id", $user_id);
            $stmt->execute();
            break;
            
        case 'toggle_room_status':
            $room_id = $_POST['room_id'];
            $new_status = $_POST['new_status'];
            $updateQuery = "UPDATE rooms SET status = :status WHERE id = :id";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(":status", $new_status);
            $stmt->bindParam(":id", $room_id);
            $stmt->execute();
            break;
    }
}

// Fetch Dashboard Statistics
$stats = [];

// Total Reservations
$query = "SELECT COUNT(*) as count FROM reservations";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Active Reservations
$query = "SELECT COUNT(*) as count FROM reservations WHERE status = 'Approved'";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['active_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total Rooms
$query = "SELECT COUNT(*) as count FROM rooms";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_rooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Available Rooms
$query = "SELECT COUNT(*) as count FROM rooms WHERE status = 'Available'";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['available_rooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total Users
$query = "SELECT COUNT(*) as count FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending Reservations
$query = "SELECT COUNT(*) as count FROM reservations WHERE status = 'Pending'";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['pending_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Recent Reservations
$query = "SELECT r.id, u.name, u.email, rm.room_number, rm.type, r.check_in_date, r.check_out_date, r.status, r.created_at 
          FROM reservations r 
          JOIN users u ON r.user_id = u.id 
          LEFT JOIN rooms rm ON r.room_id = rm.id 
          ORDER BY r.created_at DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();
$recent_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// All Reservations for management
$query = "SELECT r.id, u.name, u.email, rm.room_number, rm.type, r.check_in_date, r.check_out_date, r.status, r.created_at 
          FROM reservations r 
          JOIN users u ON r.user_id = u.id 
          LEFT JOIN rooms rm ON r.room_id = rm.id 
          ORDER BY r.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$all_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// All Rooms
$query = "SELECT * FROM rooms ORDER BY room_number";
$stmt = $conn->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// All Users
$query = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ocean Breeze Resort</title>
    <link rel="stylesheet" href="modern_style.css">
    <style>
        /* Admin Dashboard Specific Styles */
        .admin-body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #006a88 0%, #004a5c 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            display: block;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 0.95rem;
        }
        
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid #00bcd4;
        }
        
        .nav-item i {
            margin-right: 0.5rem;
            width: 20px;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 2rem;
        }
        
        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-title {
            color: #006a88;
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-icon.reservations { background: linear-gradient(45deg, #4CAF50, #45a049); }
        .stat-icon.rooms { background: linear-gradient(45deg, #2196F3, #1976D2); }
        .stat-icon.users { background: linear-gradient(45deg, #FF9800, #F57C00); }
        .stat-icon.pending { background: linear-gradient(45deg, #f44336, #d32f2f); }
        
        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            color: #006a88;
            font-weight: 700;
        }
        
        .stat-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .dashboard-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .section-header {
            background: linear-gradient(45deg, #006a88, #004a5c);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .section-content {
            padding: 1.5rem;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #006a88;
            position: sticky;
            top: 0;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-available { background: #d4edda; color: #155724; }
        .status-occupied { background: #fff3cd; color: #856404; }
        .status-maintenance { background: #f8d7da; color: #721c24; }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .btn-approve { background: #4CAF50; color: white; }
        .btn-cancel { background: #f44336; color: white; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #ff9800; color: white; }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .hidden {
            display: none;
        }
        
        .logout-btn {
            background: #f44336;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #d32f2f;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>🌊 Admin Panel</h2>
                <p>Ocean Breeze Resort</p>
            </div>
            <nav class="sidebar-nav">
                <button class="nav-item active" onclick="showSection('dashboard')">
                    📊 Dashboard
                </button>
                <button class="nav-item" onclick="showSection('reservations')">
                    📅 Reservations
                </button>
                <button class="nav-item" onclick="showSection('rooms')">
                    🏨 Manage Rooms
                </button>
                <button class="nav-item" onclick="showSection('users')">
                    👥 Users
                </button>
                <button class="nav-item" onclick="showSection('reports')">
                    📈 Reports
                </button>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="admin-header">
                <h1 class="admin-title">Dashboard</h1>
                <div class="admin-user">
                    <span>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <!-- Dashboard Section -->
            <div id="dashboard-section" class="section">
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon reservations">📅</div>
                        <div class="stat-info">
                            <h3><?= $stats['total_reservations'] ?></h3>
                            <p>Total Reservations</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon reservations">✅</div>
                        <div class="stat-info">
                            <h3><?= $stats['active_reservations'] ?></h3>
                            <p>Active Reservations</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon rooms">🏨</div>
                        <div class="stat-info">
                            <h3><?= $stats['total_rooms'] ?></h3>
                            <p>Total Rooms</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon rooms">🔓</div>
                        <div class="stat-info">
                            <h3><?= $stats['available_rooms'] ?></h3>
                            <p>Available Rooms</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon users">👥</div>
                        <div class="stat-info">
                            <h3><?= $stats['total_users'] ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon pending">⏳</div>
                        <div class="stat-info">
                            <h3><?= $stats['pending_reservations'] ?></h3>
                            <p>Pending Reservations</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Reservations -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">Recent Reservations</h3>
                    </div>
                    <div class="section-content">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Room</th>
                                    <th>Check In</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($recent_reservations, 0, 5) as $reservation): ?>
                                <tr>
                                    <td>#<?= $reservation['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($reservation['name']) ?></strong><br>
                                        <small><?= htmlspecialchars($reservation['email']) ?></small>
                                    </td>
                                    <td>
                                        Room <?= htmlspecialchars($reservation['room_number'] ?? 'N/A') ?><br>
                                        <small><?= htmlspecialchars($reservation['type'] ?? 'N/A') ?></small>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($reservation['check_in_date'] ?? 'now')) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($reservation['status']) ?>">
                                            <?= htmlspecialchars($reservation['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($reservation['status'] === 'Pending'): ?>
                                        <div class="action-buttons">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn-action btn-approve">✓ Approve</button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="btn-action btn-cancel">✗ Cancel</button>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reservations Management Section -->
            <div id="reservations-section" class="section hidden">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">All Reservations</h3>
                        <div class="action-buttons">
                            <button onclick="filterReservations('all')" class="btn-action btn-edit">All</button>
                            <button onclick="filterReservations('pending')" class="btn-action btn-cancel">Pending</button>
                            <button onclick="filterReservations('approved')" class="btn-action btn-approve">Approved</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Room</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reservations-table-body">
                                <?php foreach ($all_reservations as $reservation): ?>
                                <tr class="reservation-row" data-status="<?= strtolower($reservation['status']) ?>">
                                    <td>#<?= $reservation['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($reservation['name']) ?></strong><br>
                                        <small><?= htmlspecialchars($reservation['email']) ?></small>
                                    </td>
                                    <td>
                                        Room <?= htmlspecialchars($reservation['room_number'] ?? 'N/A') ?><br>
                                        <small><?= htmlspecialchars($reservation['type'] ?? 'N/A') ?></small>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($reservation['check_in_date'] ?? 'now')) ?></td>
                                    <td><?= date('M j, Y', strtotime($reservation['check_out_date'] ?? 'now')) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($reservation['status']) ?>">
                                            <?= htmlspecialchars($reservation['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($reservation['created_at'])) ?></td>
                                    <td>
                                        <?php if ($reservation['status'] === 'Pending'): ?>
                                        <div class="action-buttons">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn-action btn-approve">✓</button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="btn-action btn-cancel">✗</button>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rooms Management Section -->
            <div id="rooms-section" class="section hidden">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">Room Management</h3>
                        <button onclick="openAddRoomModal()" class="btn-action btn-approve">+ Add Room</button>
                    </div>
                    <div class="section-content">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Type</th>
                                    <th>Price/Night</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($room['room_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($room['type']) ?></td>
                                    <td>$<?= number_format($room['price'], 2) ?></td>
                                    <td><?= htmlspecialchars($room['capacity']) ?> guests</td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($room['status']) ?>">
                                            <?= htmlspecialchars($room['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                                <input type="hidden" name="action" value="toggle_room_status">
                                                <input type="hidden" name="new_status" value="<?= $room['status'] === 'Available' ? 'Maintenance' : 'Available' ?>">
                                                <button type="submit" class="btn-action <?= $room['status'] === 'Available' ? 'btn-cancel' : 'btn-approve' ?>">
                                                    <?= $room['status'] === 'Available' ? '🔧 Maintenance' : '✓ Available' ?>
                                                </button>
                                            </form>
                                            <button onclick="editRoom(<?= $room['id'] ?>)" class="btn-action btn-edit">✎ Edit</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Users Management Section -->
            <div id="users-section" class="section hidden">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">User Management</h3>
                        <div class="action-buttons">
                            <span>Total Users: <?= count($users) ?></span>
                        </div>
                    </div>
                    <div class="section-content">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?= $user['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="status-badge <?= $user['role'] === 'admin' ? 'status-approved' : 'status-pending' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if ($user['role'] !== 'admin' && $user['id'] != $_SESSION['user']['id']): ?>
                                        <div class="action-buttons">
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="action" value="delete_user">
                                                <button type="submit" class="btn-action btn-delete">🗑 Delete</button>
                                            </form>
                                        </div>
                                        <?php else: ?>
                                        <span class="status-badge status-approved">Protected</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div id="reports-section" class="section hidden">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">Reports & Analytics</h3>
                        <button onclick="generateReport()" class="btn-action btn-approve">📊 Generate Report</button>
                    </div>
                    <div class="section-content">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon reservations">📈</div>
                                <div class="stat-info">
                                    <h3><?= number_format(($stats['active_reservations'] / max($stats['total_reservations'], 1)) * 100, 1) ?>%</h3>
                                    <p>Approval Rate</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon rooms">🏠</div>
                                <div class="stat-info">
                                    <h3><?= number_format(($stats['available_rooms'] / max($stats['total_rooms'], 1)) * 100, 1) ?>%</h3>
                                    <p>Room Availability</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon users">💰</div>
                                <div class="stat-info">
                                    <h3>$<?= number_format($stats['active_reservations'] * 150, 2) ?></h3>
                                    <p>Estimated Revenue</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon pending">⚡</div>
                                <div class="stat-info">
                                    <h3><?= $stats['pending_reservations'] ?></h3>
                                    <p>Needs Attention</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="dashboard-section" style="margin-top: 2rem;">
                            <div class="section-header">
                                <h3 class="section-title">Monthly Overview</h3>
                            </div>
                            <div class="section-content">
                                <p>📊 Detailed analytics and charts would be implemented here with a charting library like Chart.js</p>
                                <p>📅 Monthly reservation trends</p>
                                <p>💵 Revenue tracking</p>
                                <p>🎯 Occupancy rates</p>
                                <p>👥 User growth metrics</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Navigation functionality
        function showSection(sectionName) {
            // Hide all sections
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.add('hidden'));
            
            // Show selected section
            document.getElementById(sectionName + '-section').classList.remove('hidden');
            
            // Update navigation
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => item.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update header title
            const title = document.querySelector('.admin-title');
            title.textContent = sectionName.charAt(0).toUpperCase() + sectionName.slice(1);
        }

        // Filter reservations
        function filterReservations(status) {
            const rows = document.querySelectorAll('.reservation-row');
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Room management functions
        function openAddRoomModal() {
            alert('Add Room functionality would open a modal here. This would include a form to add new rooms with room number, type, price, capacity, etc.');
        }

        function editRoom(roomId) {
            alert('Edit Room functionality would open a modal for room ID: ' + roomId);
        }

        // Reports function
        function generateReport() {
            alert('Generate Report functionality would create downloadable reports in PDF or Excel format.');
        }

        // Auto-refresh dashboard every 30 seconds
        setInterval(function() {
            if (document.querySelector('#dashboard-section:not(.hidden)')) {
                location.reload();
            }
        }, 30000);

        // Confirmation for critical actions
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to perform this action?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>
