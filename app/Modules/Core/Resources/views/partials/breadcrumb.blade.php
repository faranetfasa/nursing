@php($crumbs = $breadcrumbs ?? [])

<nav class="mb-4 text-xs text-slate-500 dark:text-slate-400" aria-label="مسیر صفحه">
    <ol class="flex flex-wrap items-center gap-1">
        <li><a href="{{ route('core.dashboard') }}" class="hover:text-primary">خانه</a></li>
        @foreach ($crumbs as $crumb)
            <li aria-hidden="true">/</li>
            <li>
                @if (! empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}" class="hover:text-primary">{{ $crumb['title'] }}</a>
                @else
                    <span class="text-slate-700 dark:text-slate-200">{{ $crumb['title'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
