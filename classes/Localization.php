<?php
class Localization {
    private $language = 'en';
    private $translations = [];
    
    public function __construct($language = 'en') {
        $this->setLanguage($language);
    }
    
    public function setLanguage($language) {
        $this->language = $language;
        $this->loadTranslations();
    }
    
    public function getLanguage() {
        return $this->language;
    }
    
    private function loadTranslations() {
        $langFile = __DIR__ . "/../lang/{$this->language}.php";
        if (file_exists($langFile)) {
            $this->translations = include $langFile;
        } else {
            // Fallback to English if language file doesn't exist
            $this->translations = include __DIR__ . "/../lang/en.php";
        }
    }
    
    public function translate($key, $default = null) {
        return $this->translations[$key] ?? $default ?? $key;
    }
    
    public function t($key, $default = null) {
        return $this->translate($key, $default);
    }
    
    public static function getAvailableLanguages() {
        return [
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'pt' => 'Português'
        ];
    }
    
    public static function detectLanguage() {
        // Check if language is set in session
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }
        
        // Check if language is set in cookie
        if (isset($_COOKIE['preferred_language'])) {
            return $_COOKIE['preferred_language'];
        }
        
        // Check browser language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $availableLanguages = array_keys(self::getAvailableLanguages());
            if (in_array($browserLang, $availableLanguages)) {
                return $browserLang;
            }
        }
        
        // Default to English
        return 'en';
    }
    
    public function setUserLanguage($language) {
        $_SESSION['language'] = $language;
        setcookie('preferred_language', $language, time() + (365 * 24 * 60 * 60), '/');
        $this->setLanguage($language);
    }
}
?>
