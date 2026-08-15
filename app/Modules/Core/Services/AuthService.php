<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\LoginHistory;
use App\Modules\Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Authentication business logic: login with username or mobile, remember me,
 * throttling, login history and logout. Controllers stay thin.
 */
class AuthService
{
    public function __construct(private readonly AuditService $audit) {}

    /**
     * @throws ValidationException when the credentials are rejected or throttled
     */
    public function login(Request $request, string $login, string $password, bool $remember = false): User
    {
        $this->ensureIsNotRateLimited($request, $login);

        $user = $this->findByLogin($login);

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey($request, $login), (int) config('core.auth.throttle_decay_seconds', 60));
            $this->recordAttempt($request, $user, $login, false, 'invalid_credentials');

            throw ValidationException::withMessages([
                'login' => __('نام کاربری/موبایل یا رمز عبور اشتباه است.'),
            ]);
        }

        if (! $user->isActive()) {
            $this->recordAttempt($request, $user, $login, false, 'inactive_account');

            throw ValidationException::withMessages([
                'login' => __('حساب کاربری شما فعال نیست. با مدیر سیستم تماس بگیرید.'),
            ]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey($request, $login));

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->saveQuietly();

        $this->recordAttempt($request, $user, $login, true);
        $this->audit->log('login', ['module' => 'Core', 'description' => 'ورود موفق به سیستم', 'user_id' => $user->id]);

        return $user;
    }

    public function logout(Request $request): void
    {
        $user = Auth::user();

        if ($user instanceof User) {
            LoginHistory::query()
                ->where('user_id', $user->id)
                ->whereNull('logged_out_at')
                ->latest('id')
                ->limit(1)
                ->update(['logged_out_at' => now()]);

            $this->audit->log('logout', ['module' => 'Core', 'description' => 'خروج از سیستم', 'user_id' => $user->id]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function findByLogin(string $login): ?User
    {
        $login = trim($login);

        return User::query()
            ->where(function ($query) use ($login): void {
                foreach (config('core.auth.login_fields', ['username', 'mobile']) as $field) {
                    $query->orWhere($field, $login);
                }
            })
            ->first();
    }

    public function recordAttempt(Request $request, ?User $user, string $login, bool $successful, ?string $reason = null): LoginHistory
    {
        $agent = (string) $request->userAgent();

        return LoginHistory::query()->create([
            'user_id' => $user?->id,
            'login' => mb_substr($login, 0, 100),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr($agent, 0, 1000),
            'device' => $this->detectDevice($agent),
            'platform' => $this->detectPlatform($agent),
            'browser' => $this->detectBrowser($agent),
            'successful' => $successful,
            'failure_reason' => $reason,
            'logged_in_at' => $successful ? now() : null,
        ]);
    }

    private function ensureIsNotRateLimited(Request $request, string $login): void
    {
        $maxAttempts = (int) config('core.auth.max_attempts', 5);

        if (! RateLimiter::tooManyAttempts($this->throttleKey($request, $login), $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request, $login));

        throw ValidationException::withMessages([
            'login' => __('تلاش‌های بیش از حد. لطفاً :seconds ثانیه دیگر تلاش کنید.', ['seconds' => $seconds]),
        ]);
    }

    private function throttleKey(Request $request, string $login): string
    {
        return 'login|'.mb_strtolower($login).'|'.$request->ip();
    }

    private function detectDevice(string $agent): string
    {
        return preg_match('/mobile|android|iphone|ipad/i', $agent) === 1 ? 'mobile' : 'desktop';
    }

    private function detectPlatform(string $agent): ?string
    {
        foreach (['Windows', 'Android', 'iPhone', 'iPad', 'Macintosh', 'Linux'] as $platform) {
            if (stripos($agent, $platform) !== false) {
                return $platform;
            }
        }

        return null;
    }

    private function detectBrowser(string $agent): ?string
    {
        foreach (['Edg' => 'Edge', 'OPR' => 'Opera', 'Chrome' => 'Chrome', 'Safari' => 'Safari', 'Firefox' => 'Firefox'] as $needle => $browser) {
            if (stripos($agent, $needle) !== false) {
                return $browser;
            }
        }

        return null;
    }
}
