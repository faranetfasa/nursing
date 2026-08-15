@extends('core::layouts.app')

@section('title', 'داشبورد')

@section('content')
    <div class="mb-5 card">
        <h2 class="text-base font-semibold">خوش آمدید، {{ auth()->user()->fullName() }}</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            این نسخه، اسکلت معماری سامانه است: هسته سیستم (احراز هویت، نقش‌ها و دسترسی‌ها، ساختار سازمانی، تنظیمات و گزارش عملیات) فعال است
            و ماژول‌های تخصصی در فازهای بعدی روی همین ساختار اضافه می‌شوند.
        </p>
    </div>

    @livewire('core.dashboard-stats')

    <div class="mt-5 card">
        <h3 class="mb-3 text-sm font-semibold">ماژول‌های ثبت‌شده</h3>
        <div class="flex flex-wrap gap-2">
            @foreach (config('modules.modules') as $module)
                <span class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">
                    {{ $module['title'] }}
                    <span class="text-slate-400">({{ $module['phase'] === 1 ? 'فعال' : 'فاز '.$module['phase'] }})</span>
                </span>
            @endforeach
        </div>
    </div>
@endsection
