@php
    $siteName = \App\Facades\Settings::get('general', 'site_name', '101GSD');
    $tagline = \App\Facades\Settings::get('general', 'tagline');
    $about = \App\Facades\Settings::get('general', 'footer_about');
    $logo = \App\Facades\Settings::get('general', 'logo', '/images/brand/logo.png');
    $footerItems = \App\Models\Menu::where('slug', 'footer')->first()?->items ?? collect();
@endphp
<footer class="bg-[var(--color-surface-dark)] text-white">
    <div class="mx-auto max-w-(--container-width) px-6 py-14 grid gap-10 md:grid-cols-3">
        <div>
            <img src="{{ asset($logo) }}" alt="{{ $siteName }}" class="h-12 w-auto mb-4">
            @if($tagline)<p class="text-sm text-white/60 mb-3">{{ $tagline }}</p>@endif
            @if($about)<p class="text-sm text-white/50 leading-relaxed">{{ $about }}</p>@endif
        </div>

        <div>
            <h3 class="font-display text-lg mb-4">Explore</h3>
            <ul class="space-y-2 text-sm text-white/60">
                @forelse($footerItems as $item)
                    <li><a href="{{ $item->resolvedUrl() }}" class="hover:text-[var(--color-primary)] transition-colors">{{ $item->label }}</a></li>
                @empty
                    <li><a href="{{ url('/about') }}" class="hover:text-[var(--color-primary)]">About</a></li>
                    <li><a href="{{ url('/refund-cancellation-policy') }}" class="hover:text-[var(--color-primary)]">Refund &amp; Cancellation Policy</a></li>
                    <li><a href="{{ url('/terms') }}" class="hover:text-[var(--color-primary)]">Terms &amp; Conditions</a></li>
                    <li><a href="{{ url('/privacy') }}" class="hover:text-[var(--color-primary)]">Privacy Policy</a></li>
                    <li><a href="{{ url('/faq') }}" class="hover:text-[var(--color-primary)]">FAQ</a></li>
                @endforelse
            </ul>
        </div>

        <div>
            <h3 class="font-display text-lg mb-4">Contact</h3>
            <p class="text-sm text-white/60">{{ \App\Facades\Settings::get('general', 'support_email', 'support@101gsd.in') }}</p>
            <p class="text-sm text-white/60">{{ \App\Facades\Settings::get('general', 'support_phone') }}</p>
        </div>
    </div>

    <div class="border-t border-white/10 py-5 text-center text-xs text-white/40">
        {{ \App\Facades\Settings::get('general', 'copyright_line', '© '.date('Y').' '.$siteName.'. All rights reserved.') }}
    </div>
</footer>
