@extends('core::layouts.guest')

@section('title', 'بازیابی رمز عبور')

@section('content')
    <h2 class="mb-1 text-base font-semibold text-slate-800 dark:text-slate-100">بازیابی رمز عبور</h2>
    <p class="mb-5 text-xs text-slate-500 dark:text-slate-400">
        نام کاربری یا موبایل خود را وارد کنید تا لینک بازیابی به ایمیل ثبت‌شده ارسال شود.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="login" class="form-label">نام کاربری یا موبایل</label>
            <input id="login" name="login" type="text" class="form-input" value="{{ old('login') }}" required autofocus dir="ltr">
        </div>

        <button type="submit" class="btn-primary w-full">ارسال لینک بازیابی</button>

        <a href="{{ route('core.login') }}" class="block text-center text-xs text-primary hover:underline">بازگشت به صفحه ورود</a>
    </form>
@endsection
