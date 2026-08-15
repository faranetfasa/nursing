<?php

namespace App\Modules\Core\Http\Controllers\Auth;

use App\Modules\Core\Http\Requests\LoginRequest;
use App\Modules\Core\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function create(): View
    {
        return view('core::auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->auth->login(
            $request,
            (string) $request->string('login'),
            (string) $request->string('password'),
            $request->remember(),
        );

        return redirect()->intended(route('core.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->auth->logout($request);

        return redirect()->route('core.login')->with('status', 'با موفقیت از سیستم خارج شدید.');
    }
}
