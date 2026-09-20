<?php

namespace Database\Seeders;

use App\Facades\Settings;
use Illuminate\Database\Seeder;

/**
 * Seeds only the defaults Phase 1 actually needs to render (General +
 * Appearance). The remaining settings tabs (Email, SMS, Payments, ...)
 * are seeded as their own screens are built in later phases.
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Settings::set('general', 'site_name', '101GSD');
        Settings::set('general', 'tagline', 'One Platform. Every GSD');
        Settings::set('general', 'logo', '/images/brand/logo.png');
        Settings::set('general', 'logo_dark', '/images/brand/logo.png');
        Settings::set('general', 'favicon', '/images/brand/logo.png');
        Settings::set('general', 'footer_about', 'The single home for German Shepherd specialty show entries in India — one login for every club, every show.');
        Settings::set('general', 'currency_symbol', '₹');
        Settings::set('general', 'timezone', 'Asia/Kolkata');

        Settings::set('appearance', 'color_primary', '#F2B90C');
        Settings::set('appearance', 'color_primary_foreground', '#16130A');
        Settings::set('appearance', 'color_secondary', '#5B7FE0');
        Settings::set('appearance', 'color_secondary_foreground', '#FFFFFF');
        Settings::set('appearance', 'color_ink', '#111111');
        Settings::set('appearance', 'color_ink_muted', '#6B7280');
        Settings::set('appearance', 'color_surface', '#FFFFFF');
        Settings::set('appearance', 'color_surface_dark', '#16130A');
        Settings::set('appearance', 'font_heading', 'Playfair Display');
        Settings::set('appearance', 'font_body', 'Instrument Sans');
        Settings::set('appearance', 'button_radius', '9999px');
        Settings::set('appearance', 'container_width', '1280px');
    }
}
