<?php

namespace App\Modules\Core\Http\Controllers\Auth;

use App\Modules\Core\Http\Requests\ForgotPasswordRequest;
use App\Modules\Core\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function create(): View
    {
        return view('core::auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = $this->auth->findByLogin((string) $request->string('login'));

        // The same response is returned for unknown accounts so the form can not
        // be used to enumerate users.
        if ($user && $user->email) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return back()->with('status', 'اگر حساب کاربری با این مشخصات وجود داشته باشد، لینک بازیابی رمز عبور برای ایمیل ثبت‌شده ارسال می‌شود.');
    }
}
