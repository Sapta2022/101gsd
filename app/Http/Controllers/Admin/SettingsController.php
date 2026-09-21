<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function general(): View
    {
        return view('admin.settings.general', [
            'general' => Settings::group('general'),
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'logo_dark' => ['nullable', 'string', 'max:2048'],
            'favicon' => ['nullable', 'string', 'max:2048'],
            'footer_about' => ['nullable', 'string'],
            'currency_symbol' => ['nullable', 'string', 'max:5'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:50'],
        ]);

        Settings::setMany('general', $data);

        return back()->with('status', 'General settings saved.');
    }

    public function appearance(): View
    {
        return view('admin.settings.appearance', [
            'appearance' => Settings::group('appearance'),
        ]);
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'color_primary' => ['required', 'string', 'max:20'],
            'color_primary_foreground' => ['required', 'string', 'max:20'],
            'color_secondary' => ['required', 'string', 'max:20'],
            'color_secondary_foreground' => ['required', 'string', 'max:20'],
            'color_ink' => ['required', 'string', 'max:20'],
            'color_ink_muted' => ['required', 'string', 'max:20'],
            'color_surface' => ['required', 'string', 'max:20'],
            'color_surface_dark' => ['required', 'string', 'max:20'],
            'font_heading' => ['nullable', 'string', 'max:100'],
            'font_body' => ['nullable', 'string', 'max:100'],
            'button_radius' => ['nullable', 'string', 'max:20'],
            'container_width' => ['nullable', 'string', 'max:20'],
        ]);

        Settings::setMany('appearance', $data);

        return back()->with('status', 'Appearance settings saved.');
    }
}
