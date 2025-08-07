<?php
require_once '../classes/User.php';
require_once '../classes/Localization.php';
session_start();

// Initialize localization
$currentLang = Localization::detectLanguage();
$lang = new Localization($currentLang);

// Clear remember token from database if user is logged in
if (isset($_SESSION['user'])) {
    $user = new User();
    $user->clearRememberToken($_SESSION['user']['id']);
}

// Clear remember cookie
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/', '', false, true);
}

// Destroy session
session_destroy();

// Redirect to login with logout message
session_start();
$_SESSION['message'] = $lang->t('logout_success');
header("Location: login.php");
exit();
?>
