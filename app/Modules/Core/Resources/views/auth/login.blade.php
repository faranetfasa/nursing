@extends('core::layouts.guest')

@section('title', 'ورود به سیستم')

@section('content')
    <h2 class="mb-1 text-base font-semibold text-slate-800 dark:text-slate-100">ورود به سیستم</h2>
    <p class="mb-5 text-xs text-slate-500 dark:text-slate-400">با نام کاربری یا شماره موبایل خود وارد شوید.</p>

    <form method="POST" action="{{ route('core.login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="login" class="form-label">نام کاربری یا موبایل</label>
            <input id="login" name="login" type="text" class="form-input" value="{{ old('login') }}"
                   required autofocus autocomplete="username" dir="ltr">
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="form-label">رمز عبور</label>
            <div class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" class="form-input pe-16"
                       required autocomplete="current-password" dir="ltr">
                <button type="button" @click="show = ! show"
                        class="absolute inset-y-0 end-2 my-auto text-xs text-slate-400 hover:text-primary">
                    <span x-text="show ? 'پنهان' : 'نمایش'"></span>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs">
            @if (config('core.auth.remember_me'))
                <label class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-primary focus:ring-primary">
                    مرا به خاطر بسپار
                </label>
            @endif

            <a href="{{ route('password.request') }}" class="text-primary hover:underline">رمز عبور را فراموش کرده‌اید؟</a>
        </div>

        <button type="submit" class="btn-primary w-full">ورود</button>
    </form>
@endsection
