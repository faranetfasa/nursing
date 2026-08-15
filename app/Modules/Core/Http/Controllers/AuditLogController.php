<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Models\AuditLog;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::query()->with('user')->latest('id')->paginate(25);

        return view('core::system.audit-logs', ['logs' => $logs]);
    }
}
