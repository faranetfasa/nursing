<?php

namespace App\Modules\Core\Http\Controllers\Auth;

use App\Modules\Core\Http\Requests\ResetPasswordRequest;
use App\Modules\Core\Models\User;
use App\Modules\Core\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function create(Request $request, string $token): View
    {
        return view('core::auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'must_change_password' => false,
                    'password_changed_at' => now(),
                ])->save();

                $this->audit->logModel('password_reset', $user, [
                    'description' => 'بازیابی رمز عبور',
                    'user_id' => $user->id,
                ]);
            }
        );

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('core.login')->with('status', 'رمز عبور با موفقیت تغییر کرد. وارد شوید.');
    }
}
