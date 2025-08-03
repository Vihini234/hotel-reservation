<?php
require_once '../classes/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $loggedInUser = $user->login($_POST['email'], $_POST['password']);
    if ($loggedInUser) {
        $_SESSION['user'] = $loggedInUser;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($_SESSION['message'])) { echo "<p style='color:green;'>".$_SESSION['message']."</p>"; unset($_SESSION['message']); } ?>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</div>
</body>
</html>
