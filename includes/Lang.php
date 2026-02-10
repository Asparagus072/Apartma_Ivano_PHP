<?php
/**
 * Language / i18n Helper
 * Usage: Lang::get('key') or __('key')
 */
class Lang {
    private static string $current = 'en';
    private static array $translations = [];
    private static array $supported = ['en', 'sl', 'de', 'cs', 'pl', 'es'];

    public static function init(): void {
        // Detect from cookie, fallback to 'en'
        $lang = $_COOKIE['lang'] ?? 'en';
        if (!in_array($lang, self::$supported)) {
            $lang = 'en';
        }

        // Allow override via ?lang= query param (and set cookie)
        if (isset($_GET['lang']) && in_array($_GET['lang'], self::$supported)) {
            $lang = $_GET['lang'];
            setcookie('lang', $lang, time() + (365 * 24 * 3600), '/');
        }

        self::$current = $lang;
        self::load($lang);
    }

    private static function load(string $lang): void {
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            // Fallback to English
            $fallback = __DIR__ . '/../lang/en.php';
            self::$translations = file_exists($fallback) ? require $fallback : [];
        }
    }

    public static function get(string $key, array $replace = []): string {
        $text = self::$translations[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $text = str_replace(':' . $k, $v, $text);
        }
        return $text;
    }

    public static function current(): string {
        return self::$current;
    }

    public static function supported(): array {
        return self::$supported;
    }

    public static function nativeName(string $code): string {
        return match($code) {
            'en' => 'English',
            'sl' => 'Slovenščina',
            'de' => 'Deutsch',
            'cs' => 'Čeština',
            'pl' => 'Polski',
            'es' => 'Español',
            default => strtoupper($code),
        };
    }

    public static function flag(string $code): string {
        return match($code) {
            'en' => 'en',
            'sl' => 'sl',
            'de' => 'de',
            'cs' => 'cs',
            'pl' => 'pl',
            'es' => 'es',
            default => '🌐',
        };
    }
}

// Convenience shorthand
function __($key, array $replace = []): string {
    return Lang::get($key, $replace);
}