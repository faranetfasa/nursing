<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ $direction ?? 'rtl' }}"
      data-default-theme="{{ $branding['theme'] ?? 'light' }}"
      style="--brand-primary: {{ $branding['primary_color'] ?? '#0d9488' }}; --brand-secondary: {{ $branding['secondary_color'] ?? '#1e293b' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '') | {{ $branding['app_name'] }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10 antialiased dark:bg-slate-900">
<div class="w-full max-w-md">
    <div class="mb-6 text-center">
        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-xl font-bold text-white">
            {{ mb_substr($branding['app_name'], 0, 1) }}
        </div>
        <h1 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ $branding['app_name'] }}</h1>
        @if (! empty($branding['organization_name']))
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $branding['organization_name'] }}</p>
        @endif
    </div>

    <div class="card">
        @include('core::partials.flash')
        @yield('content')
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">
        <button type="button" onclick="window.nursingTheme.toggle()" class="hover:text-primary">تغییر حالت روشن/تاریک</button>
    </p>
</div>
@livewireScripts
</body>
</html>
