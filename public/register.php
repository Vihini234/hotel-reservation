<?php
require_once '../classes/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $user = new User();
        if ($user->register($name, $email, $password)) {
            $_SESSION['message'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed. Email might already be taken.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($_SESSION['message'])) { echo "<p style='color:green;'>".$_SESSION['message']."</p>"; unset($_SESSION['message']); } ?>
    <form method="POST">
        Name: <input type="text" name="name" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</div>
</body>
</html>
