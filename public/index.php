<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Hotel Reservation System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Hotel Reservation System</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="rooms.php">Rooms</a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="reservation_form.php">Reservation</a>
                <?php if ($_SESSION['user']['role'] === 'staff'): ?>
                    <a href="staff_dashboard.php">Dashboard</a>
                <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user']['name']); ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Welcome!</h2>
        <p>Reserve your stay with us or manage room information easily.</p>
    </main>

    <footer>
        <p>&copy; 2025 Hotel Reservation System</p>
    </footer>
</div>
</body>
</html>
