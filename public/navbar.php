<nav>
    <a href="index.php">Home</a>
    <a href="rooms.php">Rooms</a>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="reservation_form.php">Reservation</a>
        <?php if ($_SESSION['user']['role'] === 'staff' || $_SESSION['user']['role'] === 'admin'): ?>
            <a href="manage_rooms.php">Manage Rooms</a>
        <?php endif; ?>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="admin_dashboard.php">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php">Logout (<?= $_SESSION['user']['name']; ?>)</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>
