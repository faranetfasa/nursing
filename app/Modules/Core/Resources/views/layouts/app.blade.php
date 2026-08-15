<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ $direction ?? 'rtl' }}"
      data-default-theme="{{ $branding['theme'] ?? 'light' }}"
      style="--brand-primary: {{ $branding['primary_color'] ?? '#0d9488' }}; --brand-secondary: {{ $branding['secondary_color'] ?? '#1e293b' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? '') @hasSection('title') | @endif {{ $branding['app_name'] }}</title>
    @if (! empty($branding['favicon']))
        <link rel="icon" href="{{ route('core.files.show', ['path' => $branding['favicon']]) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased dark:bg-slate-900 dark:text-slate-100">
<div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" class="flex min-h-screen">
    @include('core::partials.sidebar')

    <div class="flex min-w-0 flex-1 flex-col">
        @include('core::partials.topbar')

        <main class="flex-1 px-4 pb-10 pt-4 lg:px-6">
            @include('core::partials.breadcrumb')
            @include('core::partials.flash')

            @hasSection('header')
                <div class="mb-5">@yield('header')</div>
            @endif

            @yield('content')
            {{ $slot ?? '' }}
        </main>

        <footer class="border-t border-slate-200 px-6 py-4 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
            {{ $branding['app_name'] }} — نسخه {{ config('app.version', '1.0.0') }}
        </footer>
    </div>
</div>
@livewireScripts
</body>
</html>
