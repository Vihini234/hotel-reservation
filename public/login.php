<?php
require_once '../classes/User.php';
require_once '../classes/Localization.php';
session_start();

// Handle language switching
if (isset($_GET['lang'])) {
    $lang = new Localization();
    $lang->setUserLanguage($_GET['lang']);
    header("Location: login.php");
    exit();
}

// Initialize localization
$currentLang = Localization::detectLanguage();
$lang = new Localization($currentLang);

// Check if user is already logged in via cookie
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user'])) {
    $user = new User();
    $userData = $user->getUserByRememberToken($_COOKIE['remember_user']);
    if ($userData) {
        $_SESSION['user'] = $userData;
        header("Location: index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $loggedInUser = $user->login($_POST['email'], $_POST['password']);
    if ($loggedInUser) {
        $_SESSION['user'] = $loggedInUser;
        
        // Handle "Remember Me" functionality
        if (isset($_POST['remember_me'])) {
            // Generate a secure random token
            $rememberToken = bin2hex(random_bytes(32));
            
            // Store token in database (you'll need to add this method to User class)
            $user->setRememberToken($loggedInUser['id'], $rememberToken);
            
            // Set cookie for 30 days
            setcookie('remember_user', $rememberToken, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        }
        
        header("Location: index.php");
        exit();
    } else {
        $error = $lang->t('invalid_credentials');
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang->t('sign_in') ?> - <?= $lang->t('site_name') ?></title>
    <link rel="stylesheet" href="modern_style.css">
    <script src="assets/js/language-switcher.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>🌊 <?= $lang->t('site_name') ?></h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php"><?= $lang->t('home') ?></a></li>
                <li><a href="rooms.php"><?= $lang->t('rooms') ?></a></li>
                <li><a href="login.php" class="active"><?= $lang->t('login') ?></a></li>
            </ul>
            <div class="nav-buttons">
                <!-- Language Switcher -->
                <div class="language-switcher">
                    <select onchange="changeLanguage(this.value)" class="lang-select">
                        <?php foreach (Localization::getAvailableLanguages() as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $code === $currentLang ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <a href="register.php" class="btn-primary"><?= $lang->t('register') ?></a>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2><?= $lang->t('welcome_back') ?></h2>
                        <p><?= $lang->t('sign_in_subtitle') ?></p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-error">
                            <span class="alert-icon">⚠️</span>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-success">
                            <span class="alert-icon">✅</span>
                            <?= htmlspecialchars($_SESSION['message']) ?>
                        </div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="email"><?= $lang->t('email_address') ?></label>
                            <input type="email" id="email" name="email" required 
                                   placeholder="<?= $lang->t('enter_email') ?>"
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="password"><?= $lang->t('password') ?></label>
                            <input type="password" id="password" name="password" required 
                                   placeholder="<?= $lang->t('enter_password') ?>">
                        </div>

                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remember_me" value="1">
                                <span class="checkmark"></span>
                                <?= $lang->t('remember_me') ?>
                            </label>
                            <a href="forgot_password.php" class="forgot-link"><?= $lang->t('forgot_password') ?></a>
                        </div>

                        <button type="submit" class="btn-auth">
                            <span class="btn-text"><?= $lang->t('sign_in') ?></span>
                            <span class="btn-icon">🔐</span>
                        </button>
                    </form>

                    <div class="auth-footer">
                        <p><?= $lang->t('no_account') ?> 
                            <a href="register.php" class="auth-link"><?= $lang->t('create_account') ?></a>
                        </p>
                    </div>

                    <!-- Demo Credentials -->
                    <div class="demo-credentials">
                        <h4><?= $lang->t('demo_accounts') ?></h4>
                        <div class="demo-grid">
                            <div class="demo-card">
                                <strong><?= $lang->t('admin_account') ?></strong>
                                <p><?= $lang->t('email') ?>: admin@hotel.com</p>
                                <p><?= $lang->t('password') ?>: admin123</p>
                            </div>
                            <div class="demo-card">
                                <strong><?= $lang->t('guest_account') ?></strong>
                                <p><?= $lang->t('email') ?>: guest@hotel.com</p>
                                <p><?= $lang->t('password') ?>: guest123</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="auth-benefits">
                    <h3><?= $lang->t('why_sign_in') ?></h3>
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <span class="benefit-icon">🏨</span>
                            <div>
                                <h4><?= $lang->t('easy_reservations') ?></h4>
                                <p><?= $lang->t('easy_reservations_desc') ?></p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">🎯</span>
                            <div>
                                <h4><?= $lang->t('exclusive_offers') ?></h4>
                                <p><?= $lang->t('exclusive_offers_desc') ?></p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">📅</span>
                            <div>
                                <h4><?= $lang->t('booking_history') ?></h4>
                                <p><?= $lang->t('booking_history_desc') ?></p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">🔔</span>
                            <div>
                                <h4><?= $lang->t('notifications') ?></h4>
                                <p><?= $lang->t('notifications_desc') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= $lang->t('site_name') ?></h3>
                    <p><?= $lang->t('creating_memories') ?></p>
                </div>
                <div class="footer-section">
                    <h4><?= $lang->t('quick_links') ?></h4>
                    <ul>
                        <li><a href="index.php"><?= $lang->t('home') ?></a></li>
                        <li><a href="rooms.php"><?= $lang->t('rooms') ?></a></li>
                        <li><a href="register.php"><?= $lang->t('register') ?></a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4><?= $lang->t('contact') ?></h4>
                    <ul>
                        <li><?= $lang->t('phone') ?>: +1 (555) 123-4567</li>
                        <li><?= $lang->t('email') ?>: info@oceanbreezeresort.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 <?= $lang->t('site_name') ?>. <?= $lang->t('all_rights_reserved') ?></p>
            </div>
        </div>
    </footer>

    <script>
        // Language switching function
        function changeLanguage(lang) {
            window.location.href = 'login.php?lang=' + lang;
        }

        // Auto-fill demo credentials
        function fillDemo(type) {
            if (type === 'admin') {
                document.getElementById('email').value = 'admin@hotel.com';
                document.getElementById('password').value = 'admin123';
            } else if (type === 'guest') {
                document.getElementById('email').value = 'guest@hotel.com';
                document.getElementById('password').value = 'guest123';
            }
        }

        // Add click handlers to demo cards
        document.addEventListener('DOMContentLoaded', function() {
            const demoCrads = document.querySelectorAll('.demo-card');
            demoCrads.forEach((card, index) => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function() {
                    if (index === 0) fillDemo('admin');
                    if (index === 1) fillDemo('guest');
                });
            });
        });
    </script>
</body>
</html>
