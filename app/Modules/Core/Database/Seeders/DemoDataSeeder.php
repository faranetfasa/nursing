<?php

namespace App\Modules\Core\Database\Seeders;

use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\Department;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Position;
use App\Modules\Core\Models\User;
use Illuminate\Database\Seeder;

/**
 * Optional demo data offered by the installer (specification item 12).
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if (! $organization) {
            return;
        }

        $branch = Branch::query()->firstOrCreate(
            ['organization_id' => $organization->id, 'code' => 'BR-2'],
            ['name' => 'شعبه شمال', 'city' => 'تهران', 'is_active' => true]
        );

        $departments = [
            'NUR' => 'دپارتمان پرستاری',
            'HR' => 'دپارتمان منابع انسانی',
            'FIN' => 'دپارتمان مالی',
        ];

        $created = [];

        foreach ($departments as $code => $name) {
            $created[$code] = Department::query()->firstOrCreate(
                ['organization_id' => $organization->id, 'code' => $code],
                ['branch_id' => $branch->id, 'name' => $name, 'is_active' => true]
            );
        }

        $positions = [
            'NRS' => ['title' => 'پرستار', 'department' => 'NUR', 'level' => 2],
            'SUP' => ['title' => 'سرپرست پرستاری', 'department' => 'NUR', 'level' => 4],
            'HRE' => ['title' => 'کارشناس منابع انسانی', 'department' => 'HR', 'level' => 3],
        ];

        foreach ($positions as $code => $definition) {
            Position::query()->firstOrCreate(
                ['organization_id' => $organization->id, 'code' => $code],
                [
                    'department_id' => $created[$definition['department']]->id,
                    'title' => $definition['title'],
                    'level' => $definition['level'],
                    'is_active' => true,
                ]
            );
        }

        $users = [
            ['username' => 'nurse.demo', 'mobile' => '09120000001', 'name' => 'پرستار نمونه', 'role' => 'nurse', 'department' => 'NUR'],
            ['username' => 'hr.demo', 'mobile' => '09120000002', 'name' => 'کارشناس منابع انسانی نمونه', 'role' => 'hr-manager', 'department' => 'HR'],
            ['username' => 'supervisor.demo', 'mobile' => '09120000003', 'name' => 'سرپرست نمونه', 'role' => 'supervisor', 'department' => 'NUR'],
        ];

        foreach ($users as $definition) {
            $user = User::query()->updateOrCreate(
                ['username' => $definition['username']],
                [
                    'organization_id' => $organization->id,
                    'branch_id' => $branch->id,
                    'department_id' => $created[$definition['department']]->id,
                    'name' => $definition['name'],
                    'mobile' => $definition['mobile'],
                    'password' => 'Demo@12345',
                    'status' => User::STATUS_ACTIVE,
                ]
            );

            $user->syncRoles([$definition['role']]);
        }
    }
}
