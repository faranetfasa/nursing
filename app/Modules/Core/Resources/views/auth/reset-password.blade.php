@extends('core::layouts.guest')

@section('title', 'تغییر رمز عبور')

@section('content')
    <h2 class="mb-5 text-base font-semibold text-slate-800 dark:text-slate-100">تعیین رمز عبور جدید</h2>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="form-label">ایمیل</label>
            <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $email) }}" required dir="ltr">
        </div>

        <div>
            <label for="password" class="form-label">رمز عبور جدید</label>
            <input id="password" name="password" type="password" class="form-input" required dir="ltr">
        </div>

        <div>
            <label for="password_confirmation" class="form-label">تکرار رمز عبور</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" required dir="ltr">
        </div>

        <button type="submit" class="btn-primary w-full">ثبت رمز عبور</button>
    </form>
@endsection
