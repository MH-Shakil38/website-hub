<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Support\LandingLocale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Super Admin -> Language: picks the language of the public SaaS landing page
 * (Bangla by default, English or Arabic with RTL) and whether visitors may switch it.
 */
class SaasLanguageController extends Controller
{
    public function edit()
    {
        return view('admin.saas-language', [
            'languages' => LandingLocale::supported(),
            'currentLocale' => LandingLocale::adminDefault(),
            'switcherEnabled' => LandingLocale::switcherEnabled(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'landing_locale' => ['required', 'string', Rule::in(array_keys(LandingLocale::supported()))],
            'language_switcher' => ['nullable', 'boolean'],
        ]);

        Setting::setValue(LandingLocale::SETTING_LOCALE, $data['landing_locale'], 'language');
        Setting::setValue(LandingLocale::SETTING_SWITCHER, $request->boolean('language_switcher') ? '1' : '0', 'language');

        $name = LandingLocale::supported()[$data['landing_locale']]['name'];

        ActivityLog::record(
            'Landing Page Language Updated',
            "SaaS landing page language set to {$name}",
            'bi-translate',
            '#E0F2FE',
            '#0284C7'
        );

        return back()->with('success', "Landing page language saved: {$name}.");
    }
}
