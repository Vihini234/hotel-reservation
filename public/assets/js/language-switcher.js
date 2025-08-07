// Enhanced Language Switcher JavaScript
class LanguageSwitcher {
    constructor() {
        this.init();
    }

    init() {
        // Add event listeners to all language selectors
        const selectors = document.querySelectorAll('.lang-select, .language-select');
        selectors.forEach(selector => {
            selector.addEventListener('change', (e) => {
                this.changeLanguage(e.target.value);
            });
        });

        // Add keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.altKey && e.key >= '1' && e.key <= '4') {
                const languages = ['en', 'es', 'fr', 'pt'];
                const langIndex = parseInt(e.key) - 1;
                if (languages[langIndex]) {
                    this.changeLanguage(languages[langIndex]);
                }
            }
        });

        // Add language detection and auto-switch
        this.detectBrowserLanguage();
    }

    changeLanguage(langCode) {
        // Show loading indicator
        this.showLoadingIndicator();

        // Set language cookie
        this.setLanguageCookie(langCode);

        // Reload page with language parameter
        const url = new URL(window.location);
        url.searchParams.set('lang', langCode);
        window.location.href = url.toString();
    }

    setLanguageCookie(langCode) {
        // Set cookie for 1 year
        const expiryDate = new Date();
        expiryDate.setFullYear(expiryDate.getFullYear() + 1);
        document.cookie = `user_language=${langCode}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
    }

    detectBrowserLanguage() {
        // Only auto-detect if no language is set
        const urlParams = new URLSearchParams(window.location.search);
        const hasLangParam = urlParams.has('lang');
        const hasCookie = document.cookie.includes('user_language=');

        if (!hasLangParam && !hasCookie) {
            const browserLang = navigator.language.split('-')[0];
            const supportedLanguages = ['en', 'es', 'fr', 'pt'];
            
            if (supportedLanguages.includes(browserLang) && browserLang !== 'en') {
                // Auto-switch to browser language if supported
                setTimeout(() => {
                    this.changeLanguage(browserLang);
                }, 1000);
            }
        }
    }

    showLoadingIndicator() {
        // Create and show loading overlay
        const overlay = document.createElement('div');
        overlay.id = 'language-loading';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <p>Changing language...</p>
            </div>
        `;
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            backdrop-filter: blur(5px);
        `;

        const loadingContent = overlay.querySelector('.loading-content');
        loadingContent.style.cssText = `
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        `;

        const spinner = overlay.querySelector('.loading-spinner');
        spinner.style.cssText = `
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #006a88;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        `;

        // Add CSS animation
        if (!document.querySelector('#spinner-css')) {
            const style = document.createElement('style');
            style.id = 'spinner-css';
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(overlay);
    }

    // Utility method to get current language
    getCurrentLanguage() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('lang')) {
            return urlParams.get('lang');
        }

        const cookie = document.cookie.split(';').find(c => c.trim().startsWith('user_language='));
        if (cookie) {
            return cookie.split('=')[1];
        }

        return 'en'; // Default
    }

    // Method to update language-dependent content dynamically
    updateContent(translations) {
        const elements = document.querySelectorAll('[data-translate]');
        elements.forEach(element => {
            const key = element.getAttribute('data-translate');
            if (translations[key]) {
                element.textContent = translations[key];
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LanguageSwitcher();
});

// Global function for backward compatibility
function changeLanguage(lang) {
    const switcher = new LanguageSwitcher();
    switcher.changeLanguage(lang);
}
