@props(['settings', 'seo'])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seo->title }}</title>
    <meta name="description" content="{{ $seo->description }}">
    <link rel="canonical" href="{{ $seo->canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo->title }}">
    <meta property="og:description" content="{{ $seo->description }}">
    <meta property="og:url" content="{{ $seo->canonical }}">
    @if ($seo->image)<meta property="og:image" content="{{ $seo->image }}">@endif
    <script>document.documentElement.classList.toggle('dark', localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && matchMedia('(prefers-color-scheme: dark)').matches));</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
    <canvas id="ambient-scene" class="ambient-scene" aria-hidden="true"></canvas>
    <a href="#main-content" class="sr-only z-[60] rounded-lg bg-cyan-300 px-4 py-2 text-slate-950 focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to content</a>
    <x-site.navigation :settings="$settings" />
    <main id="main-content" class="site-layer">{{ $slot }}</main>
    <div class="site-layer"><x-site.footer :settings="$settings" /></div>
</body>
</html>
