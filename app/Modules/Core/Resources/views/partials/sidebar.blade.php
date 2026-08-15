@php($menu = App\Modules\Core\Support\Menu::items())

<aside x-show="sidebarOpen" x-cloak
       class="fixed inset-y-0 z-40 w-64 shrink-0 border-l border-slate-200 bg-white p-4 lg:static lg:block dark:border-slate-700 dark:bg-slate-800">
    <div class="mb-6 flex items-center gap-3">
        @if (! empty($branding['logo']))
            <img src="{{ route('core.files.show', ['path' => $branding['logo']]) }}" alt="لوگو" class="h-10 w-10 rounded-lg object-cover">
        @else
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-lg font-bold text-white">
                {{ mb_substr($branding['app_name'], 0, 1) }}
            </span>
        @endif
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold">{{ $branding['app_name'] }}</p>
            <p class="truncate text-xs text-slate-400">{{ $branding['organization_name'] ?: 'پنل مدیریت' }}</p>
        </div>
    </div>

    <nav class="space-y-1">
        @foreach ($menu as $item)
            @continue(! empty($item['permission']) && ! auth()->user()?->can($item['permission']) && ! auth()->user()?->is_super_admin)
            <a href="{{ $item['url'] }}"
               class="sidebar-link {{ request()->routeIs($item['route_pattern'] ?? '') ? 'sidebar-link-active' : '' }}">
                <span aria-hidden="true">{!! $item['icon'] !!}</span>
                <span>{{ $item['title'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-6 rounded-lg bg-slate-50 p-3 text-xs leading-relaxed text-slate-500 dark:bg-slate-700/40 dark:text-slate-400">
        ماژول‌های بعدی (منابع انسانی، بیماران، مالی، ...) در فازهای آینده به همین منو اضافه می‌شوند.
    </div>
</aside>
