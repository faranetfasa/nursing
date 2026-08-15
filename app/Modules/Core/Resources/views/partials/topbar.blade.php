<header class="sticky top-0 z-30 flex items-center justify-between gap-4 border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur lg:px-6 dark:border-slate-700 dark:bg-slate-800/90">
    <div class="flex items-center gap-3">
        <button type="button" class="btn-ghost lg:hidden" @click="sidebarOpen = ! sidebarOpen" aria-label="نمایش منو">☰</button>
        <h1 class="text-sm font-semibold text-slate-700 dark:text-slate-200">@yield('title', 'داشبورد')</h1>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" class="btn-ghost" onclick="window.nursingTheme.toggle()" title="حالت روشن/تاریک">
            <span class="hidden sm:inline">حالت نمایش</span>
            <span aria-hidden="true">◐</span>
        </button>

        @include('core::partials.user-menu')
    </div>
</header>
