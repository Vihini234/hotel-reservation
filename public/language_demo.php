<?php
require_once '../classes/Localization.php';
require_once 'language_switcher.php';

// Initialize localization
$currentLang = Localization::detectLanguage();
$lang = new Localization($currentLang);

// Handle language switching
if (isset($_GET['lang'])) {
    $localization = new Localization();
    $localization->setUserLanguage($_GET['lang']);
    header("Location: language_demo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?= $lang->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang->t('language') ?> Demo - <?= $lang->t('site_name') ?></title>
    <link rel="stylesheet" href="modern_style.css">
    <script src="assets/js/language-switcher.js"></script>
    <style>
        .demo-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 2px solid #e5f4f7;
        }
        
        .demo-title {
            color: #006a88;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .demo-description {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .demo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px dashed #006a88;
        }
        
        .keyboard-shortcuts {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .shortcut-key {
            background: #006a88;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .feature-list li::before {
            content: '✅';
            font-size: 1.1rem;
        }
        
        .toggle-menu-js {
            display: none;
        }
    </style>
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
                <li><a href="login.php"><?= $lang->t('login') ?></a></li>
            </ul>
            <div class="nav-buttons">
                <!-- Default Language Switcher -->
                <?php renderLanguageSwitcher($currentLang, 'dropdown'); ?>
                <a href="register.php" class="btn-primary"><?= $lang->t('register') ?></a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="auth-section">
        <div class="container">
            <div style="max-width: 1000px; margin: 0 auto;">
                <h1 style="text-align: center; color: #006a88; margin: 2rem 0;">
                    🌍 Language Switcher Demo
                </h1>
                
                <!-- Current Language Info -->
                <div class="demo-section">
                    <h3 class="demo-title">Current Language: <?= $lang->t('language') ?></h3>
                    <p class="demo-description">
                        <strong>Active:</strong> <?= Localization::getAvailableLanguages()[$currentLang] ?? 'Unknown' ?> (<?= $currentLang ?>)<br>
                        <strong>Site Name:</strong> <?= $lang->t('site_name') ?><br>
                        <strong>Welcome Message:</strong> <?= $lang->t('welcome_back') ?>
                    </p>
                </div>

                <!-- Dropdown Style -->
                <div class="demo-section">
                    <h3 class="demo-title">1. Dropdown Style (Default)</h3>
                    <p class="demo-description">
                        Classic dropdown selector with flags and language names. Best for header navigation.
                    </p>
                    <div class="demo-container">
                        <?php renderLanguageSwitcher($currentLang, 'dropdown'); ?>
                    </div>
                </div>

                <!-- Button Style -->
                <div class="demo-section">
                    <h3 class="demo-title">2. Button Style</h3>
                    <p class="demo-description">
                        Individual buttons for each language with hover effects. Great for settings pages.
                    </p>
                    <div class="demo-container">
                        <?php renderLanguageSwitcher($currentLang, 'buttons'); ?>
                    </div>
                </div>

                <!-- Flag Style -->
                <div class="demo-section">
                    <h3 class="demo-title">3. Flag Style (Compact)</h3>
                    <p class="demo-description">
                        Flag-only buttons for minimal space usage. Perfect for mobile or compact layouts.
                    </p>
                    <div class="demo-container">
                        <?php renderLanguageSwitcher($currentLang, 'flags'); ?>
                    </div>
                </div>

                <!-- Custom Dropdown Style -->
                <div class="demo-section">
                    <h3 class="demo-title">4. Custom Dropdown Style</h3>
                    <p class="demo-description">
                        Enhanced dropdown with custom styling and smooth animations. Most interactive option.
                    </p>
                    <div class="demo-container">
                        <?php renderCustomLanguageSwitcher($currentLang, true, true); ?>
                    </div>
                </div>

                <!-- Features -->
                <div class="demo-section">
                    <h3 class="demo-title">✨ Enhanced Features</h3>
                    <ul class="feature-list">
                        <li>Automatic browser language detection</li>
                        <li>Cookie-based language preference storage</li>
                        <li>Smooth loading transitions</li>
                        <li>Keyboard shortcuts for quick switching</li>
                        <li>Mobile responsive design</li>
                        <li>Accessibility features</li>
                        <li>SEO-friendly language switching</li>
                    </ul>
                    
                    <div class="keyboard-shortcuts">
                        <strong>⌨️ Keyboard Shortcuts:</strong><br>
                        <span class="shortcut-key">Alt + 1</span> English &nbsp;
                        <span class="shortcut-key">Alt + 2</span> Español &nbsp;
                        <span class="shortcut-key">Alt + 3</span> Français &nbsp;
                        <span class="shortcut-key">Alt + 4</span> Português
                    </div>
                </div>

                <!-- Implementation Guide -->
                <div class="demo-section">
                    <h3 class="demo-title">🔧 Implementation Guide</h3>
                    <div class="demo-description">
                        <strong>1. Include the components:</strong><br>
                        <code style="background: #f5f5f5; padding: 0.2rem 0.5rem; border-radius: 4px;">
                            require_once 'language_switcher.php';
                        </code><br><br>
                        
                        <strong>2. Render the switcher:</strong><br>
                        <code style="background: #f5f5f5; padding: 0.2rem 0.5rem; border-radius: 4px;">
                            &lt;?php renderLanguageSwitcher($currentLang, 'dropdown'); ?&gt;
                        </code><br><br>
                        
                        <strong>3. Add the JavaScript:</strong><br>
                        <code style="background: #f5f5f5; padding: 0.2rem 0.5rem; border-radius: 4px;">
                            &lt;script src="assets/js/language-switcher.js"&gt;&lt;/script&gt;
                        </code>
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
                        <li><a href="login.php"><?= $lang->t('login') ?></a></li>
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
        // Custom dropdown toggle functionality
        function toggleLanguageMenu() {
            const switcher = document.querySelector('.custom-language-switcher');
            const menu = document.getElementById('language-menu');
            
            if (switcher && menu) {
                switcher.classList.toggle('open');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const switcher = document.querySelector('.custom-language-switcher');
            if (switcher && !switcher.contains(event.target)) {
                switcher.classList.remove('open');
            }
        });

        // Show feature highlight on load
        document.addEventListener('DOMContentLoaded', function() {
            // Add some interactive demos
            const demoContainers = document.querySelectorAll('.demo-container');
            demoContainers.forEach(container => {
                container.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.3s ease';
                });
                
                container.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
