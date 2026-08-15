@auth
    <div x-data="{ open: false }" class="relative">
        <button type="button" @click="open = ! open" class="btn-ghost">
            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                {{ auth()->user()->initials() }}
            </span>
            <span class="hidden sm:inline">{{ auth()->user()->fullName() }}</span>
        </button>

        <div x-show="open" x-cloak @click.outside="open = false"
             class="absolute end-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg dark:border-slate-700 dark:bg-slate-800">
            <div class="px-3 py-2 text-xs text-slate-400">
                {{ auth()->user()->username }}
                @if (auth()->user()->roles->isNotEmpty())
                    <div class="mt-1 text-slate-500 dark:text-slate-300">
                        {{ auth()->user()->roles->map(fn ($role) => $role->label())->implode('، ') }}
                    </div>
                @endif
            </div>

            <a href="{{ route('core.account.login-history') }}" class="sidebar-link">تاریخچه ورود</a>

            <form method="POST" action="{{ route('core.logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-start text-rose-600 dark:text-rose-400">خروج از سیستم</button>
            </form>
        </div>
    </div>
@endauth
