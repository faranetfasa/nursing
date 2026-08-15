<?php

namespace App\Modules\Core\Http\Livewire;

use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\Department;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class DashboardStats extends Component
{
    public function stats(): array
    {
        return [
            ['title' => 'کاربران فعال', 'value' => User::query()->where('status', User::STATUS_ACTIVE)->count()],
            ['title' => 'نقش‌ها', 'value' => Role::query()->count()],
            ['title' => 'شعبه‌ها', 'value' => Branch::query()->count()],
            ['title' => 'دپارتمان‌ها', 'value' => Department::query()->count()],
        ];
    }

    public function render(): View
    {
        return view('core::livewire.dashboard-stats', ['stats' => $this->stats()]);
    }
}
