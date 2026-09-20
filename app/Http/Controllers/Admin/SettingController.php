<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    protected const TEXT_KEYS = [
        'site_name', 'tagline', 'phone', 'whatsapp', 'email', 'address', 'opening_hours',
        'facebook_url', 'instagram_url', 'currency_symbol', 'timezone',
        'color_primary', 'color_secondary', 'color_accent',
        'hero_title', 'hero_subtitle', 'announcement_text',
        'logo_path', 'hero_image_path',
    ];

    protected const BOOL_KEYS = [
        'lang_fr_active', 'lang_en_active', 'announcement_active',
        'show_featured_products', 'show_producers', 'show_testimonials', 'show_newsletter',
    ];

    public function edit()
    {
        $settings = [];
        foreach (self::TEXT_KEYS as $key) {
            $settings[$key] = Setting::get($key);
        }
        foreach (self::BOOL_KEYS as $key) {
            $settings[$key] = Setting::getBool($key, true);
        }

        return Inertia::render('Admin/Settings/Edit', [
            'settings' => $settings,
            'logoUrl' => $settings['logo_path'] ? asset('fichiers/'.$settings['logo_path']) : null,
            'heroImageUrl' => $settings['hero_image_path'] ? asset('fichiers/'.$settings['hero_image_path']) : null,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'opening_hours' => ['nullable', 'string', 'max:500'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'currency_symbol' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'timezone'],
            'color_primary' => ['nullable', 'string', 'max:20'],
            'color_secondary' => ['nullable', 'string', 'max:20'],
            'color_accent' => ['nullable', 'string', 'max:20'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'announcement_text' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'hero_image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            if ($old = Setting::get('logo_path')) {
                Storage::disk('public')->delete($old);
            }
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('hero_image')) {
            if ($old = Setting::get('hero_image_path')) {
                Storage::disk('public')->delete($old);
            }
            $data['hero_image_path'] = $request->file('hero_image')->store('branding', 'public');
        }

        unset($data['logo'], $data['hero_image']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        foreach (self::BOOL_KEYS as $key) {
            Setting::set($key, $request->boolean($key) ? '1' : '0');
        }

        return back()->with('success', 'Paramètres mis à jour.');
    }
}
