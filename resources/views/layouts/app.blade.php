<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo-meta')
    @include('partials.appearance-vars')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-[var(--color-ink)] antialiased">
    @include('partials.site-header')

    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @include('partials.site-footer')
</body>
</html>
