<?php

namespace App\Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('core::dashboard.index');
    }
}
