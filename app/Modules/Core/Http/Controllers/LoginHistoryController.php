<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class LoginHistoryController extends Controller
{
    /** Login history of the authenticated user (specification item 15). */
    public function index(Request $request): View
    {
        $histories = LoginHistory::query()
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->latest('id')
            ->paginate((int) config('core.auth.login_history_page_size', 20));

        return view('core::account.login-history', ['histories' => $histories]);
    }
}
