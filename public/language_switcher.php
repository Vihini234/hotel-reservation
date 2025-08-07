<?php
// Reusable Language Switcher Component
// Usage: include 'language_switcher.php';

require_once __DIR__ . '/../classes/Localization.php';

function renderLanguageSwitcher($currentLang = null, $style = 'dropdown') {
    if (!$currentLang) {
        $currentLang = Localization::detectLanguage();
    }
    
    $languages = Localization::getAvailableLanguages();
    $languageFlags = [
        'en' => '🇺🇸',
        'es' => '🇪🇸', 
        'fr' => '🇫🇷',
        'pt' => '🇧🇷'
    ];
    
    if ($style === 'dropdown') {
        echo '<div class="language-switcher">';
        echo '<select class="lang-select" onchange="changeLanguage(this.value)" title="Change Language">';
        
        foreach ($languages as $code => $name) {
            $flag = $languageFlags[$code] ?? '🌐';
            $selected = $code === $currentLang ? 'selected' : '';
            echo "<option value=\"{$code}\" {$selected}>{$flag} {$name}</option>";
        }
        
        echo '</select>';
        echo '</div>';
        
    } elseif ($style === 'buttons') {
        echo '<div class="language-buttons">';
        
        foreach ($languages as $code => $name) {
            $flag = $languageFlags[$code] ?? '🌐';
            $active = $code === $currentLang ? 'active' : '';
            echo "<button class=\"lang-btn {$active}\" onclick=\"changeLanguage('{$code}')\" title=\"{$name}\">";
            echo "<span class=\"flag\">{$flag}</span>";
            echo "<span class=\"lang-name\">{$name}</span>";
            echo "</button>";
        }
        
        echo '</div>';
        
    } elseif ($style === 'flags') {
        echo '<div class="language-flags">';
        
        foreach ($languages as $code => $name) {
            $flag = $languageFlags[$code] ?? '🌐';
            $active = $code === $currentLang ? 'active' : '';
            echo "<button class=\"flag-btn {$active}\" onclick=\"changeLanguage('{$code}')\" title=\"{$name}\">";
            echo "<span class=\"flag-icon\">{$flag}</span>";
            echo "</button>";
        }
        
        echo '</div>';
    }
}

// Function to render language switcher with custom styling
function renderCustomLanguageSwitcher($currentLang = null, $showFlags = true, $showNames = true) {
    if (!$currentLang) {
        $currentLang = Localization::detectLanguage();
    }
    
    $languages = Localization::getAvailableLanguages();
    $languageFlags = [
        'en' => '🇺🇸',
        'es' => '🇪🇸', 
        'fr' => '🇫🇷',
        'pt' => '🇧🇷'
    ];
    
    echo '<div class="custom-language-switcher">';
    echo '<div class="current-language" onclick="toggleLanguageMenu()">';
    
    $currentFlag = $languageFlags[$currentLang] ?? '🌐';
    $currentName = $languages[$currentLang] ?? 'English';
    
    if ($showFlags) echo "<span class=\"current-flag\">{$currentFlag}</span>";
    if ($showNames) echo "<span class=\"current-name\">{$currentName}</span>";
    echo '<span class="dropdown-arrow">▼</span>';
    echo '</div>';
    
    echo '<div class="language-menu" id="language-menu">';
    foreach ($languages as $code => $name) {
        if ($code === $currentLang) continue;
        
        $flag = $languageFlags[$code] ?? '🌐';
        echo "<div class=\"language-option\" onclick=\"changeLanguage('{$code}')\">";
        if ($showFlags) echo "<span class=\"option-flag\">{$flag}</span>";
        if ($showNames) echo "<span class=\"option-name\">{$name}</span>";
        echo "</div>";
    }
    echo '</div>';
    echo '</div>';
}
?>
