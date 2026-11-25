<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Language of the public SaaS landing page (central domain '/').
 *
 * The Super Admin picks the default language from Admin -> Language.
 * Bangla is the default when nothing has been saved yet. When the admin
 * turns on the visitor switcher, a visitor can pick another language with
 * ?lang=xx, which is remembered in their session.
 */
class LandingLocale
{
    public const DEFAULT = 'bn';

    public const SETTING_LOCALE = 'saas_landing_locale';

    public const SETTING_SWITCHER = 'saas_landing_language_switcher';

    public const SESSION_KEY = 'saas_landing_locale';

    /**
     * @return array<string, array{name: string, native: string, dir: string, flag: string}>
     */
    public static function supported(): array
    {
        return [
            'bn' => ['name' => 'Bangla', 'native' => 'বাংলা', 'dir' => 'ltr', 'flag' => '🇧🇩'],
            'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr', 'flag' => '🇬🇧'],
            'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'dir' => 'rtl', 'flag' => '🇸🇦'],
        ];
    }

    public static function isSupported(?string $locale): bool
    {
        return is_string($locale) && array_key_exists($locale, static::supported());
    }

    /** The language the Super Admin chose for the landing page (Bangla if never set). */
    public static function adminDefault(): string
    {
        $value = static::read(static::SETTING_LOCALE, static::DEFAULT);

        return static::isSupported($value) ? $value : static::DEFAULT;
    }

    public static function switcherEnabled(): bool
    {
        return (string) static::read(static::SETTING_SWITCHER, '0') === '1';
    }

    /**
     * Work out the language for this request. Honors ?lang= and the visitor's
     * session only when the admin has enabled the visitor language switcher.
     */
    public static function resolve(?Request $request = null): string
    {
        $default = static::adminDefault();

        if (! static::switcherEnabled() || ! $request) {
            return $default;
        }

        $requested = $request->query('lang');
        if (static::isSupported($requested)) {
            if ($request->hasSession()) {
                $request->session()->put(static::SESSION_KEY, $requested);
            }

            return $requested;
        }

        $fromSession = $request->hasSession() ? $request->session()->get(static::SESSION_KEY) : null;

        return static::isSupported($fromSession) ? $fromSession : $default;
    }

    public static function direction(string $locale): string
    {
        return static::supported()[$locale]['dir'] ?? 'ltr';
    }

    protected static function read(string $key, mixed $default): mixed
    {
        try {
            if (! Schema::connection('central')->hasTable('core_settings')) {
                return $default;
            }

            return Setting::getValue($key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}
