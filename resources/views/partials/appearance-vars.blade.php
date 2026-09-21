{{-- Overrides the compiled Tailwind theme defaults at runtime from
     Settings > Appearance, so a color/font change there needs no
     rebuild. Falls back to the same values baked into resources/css/app.css. --}}
<style>
    :root {
        --color-primary: {{ \App\Facades\Settings::get('appearance', 'color_primary', '#F2B90C') }};
        --color-primary-foreground: {{ \App\Facades\Settings::get('appearance', 'color_primary_foreground', '#16130A') }};
        --color-secondary: {{ \App\Facades\Settings::get('appearance', 'color_secondary', '#5B7FE0') }};
        --color-secondary-foreground: {{ \App\Facades\Settings::get('appearance', 'color_secondary_foreground', '#FFFFFF') }};
        --color-ink: {{ \App\Facades\Settings::get('appearance', 'color_ink', '#111111') }};
        --color-ink-muted: {{ \App\Facades\Settings::get('appearance', 'color_ink_muted', '#6B7280') }};
        --color-surface: {{ \App\Facades\Settings::get('appearance', 'color_surface', '#FFFFFF') }};
        --color-surface-dark: {{ \App\Facades\Settings::get('appearance', 'color_surface_dark', '#16130A') }};
        --radius-button: {{ \App\Facades\Settings::get('appearance', 'button_radius', '9999px') }};
        --container-width: {{ \App\Facades\Settings::get('appearance', 'container_width', '1280px') }};
    }
</style>
