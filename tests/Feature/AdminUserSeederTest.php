<?php

namespace Tests\Feature;

use App\Modules\Core\Database\Seeders\AdminUserSeeder;
use App\Modules\Core\Database\Seeders\PermissionSeeder;
use App\Modules\Core\Database\Seeders\RoleSeeder;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
    }

    public function test_persian_organization_names_do_not_collide_on_the_slug(): void
    {
        $this->seedAdmin([
            'organization_name' => 'موسسه اول',
            'admin_username' => 'first',
            'admin_mobile' => '09120000001',
            'admin_email' => 'first@example.com',
            'admin_password' => 'Secret@12345',
        ]);
        $this->seedAdmin([
            'organization_name' => 'موسسه دوم',
            'admin_username' => 'second',
            'admin_mobile' => '09120000002',
            'admin_email' => 'second@example.com',
            'admin_password' => 'Secret@12345',
        ]);

        $this->assertSame(2, Organization::query()->count());
        $this->assertEqualsCanonicalizing(
            ['موسسه اول', 'موسسه دوم'],
            Organization::query()->pluck('name')->all()
        );
    }

    public function test_the_organization_details_are_updated_on_a_second_run(): void
    {
        $this->seedAdmin(['organization_name' => 'موسسه اول', 'organization_city' => 'شیراز', 'admin_password' => 'Secret@12345']);
        $this->seedAdmin(['organization_name' => 'موسسه اول', 'organization_city' => 'تهران', 'admin_password' => 'Secret@12345']);

        $this->assertSame(1, Organization::query()->count());
        $this->assertSame('تهران', Organization::query()->first()->city);
    }

    public function test_a_random_password_is_generated_when_none_is_supplied(): void
    {
        $this->seedAdmin(['organization_name' => 'موسسه', 'admin_username' => 'admin']);

        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->assertFalse(Hash::check('Admin@12345', $admin->password));
    }

    /** @param array<string, mixed> $data */
    private function seedAdmin(array $data): void
    {
        AdminUserSeeder::$data = $data;

        try {
            $this->seed(AdminUserSeeder::class);
        } finally {
            AdminUserSeeder::$data = [];
        }
    }
}
