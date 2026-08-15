<?php

namespace Tests\Feature;

use App\Modules\Core\Database\Seeders\PermissionSeeder;
use App\Modules\Core\Database\Seeders\RoleSeeder;
use App\Modules\Core\Models\LoginHistory;
use App\Modules\Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
    }

    public function test_login_screen_is_reachable(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('ورود به سیستم', false);
    }

    public function test_user_can_login_with_username(): void
    {
        $user = User::factory()->create(['username' => 'jane', 'password' => 'secret1234']);
        $user->assignRole('operator');

        $response = $this->post('/login', ['login' => 'jane', 'password' => 'secret1234']);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_mobile(): void
    {
        $user = User::factory()->create(['mobile' => '09121112233', 'password' => 'secret1234']);
        $user->assignRole('operator');

        $this->post('/login', ['login' => '09121112233', 'password' => 'secret1234'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_remember_me_sets_the_remember_cookie(): void
    {
        $user = User::factory()->create(['username' => 'remembered', 'password' => 'secret1234']);

        $this->post('/login', ['login' => 'remembered', 'password' => 'secret1234', 'remember' => '1'])
            ->assertCookie(auth()->guard()->getRecallerName());
    }

    public function test_login_fails_with_wrong_password_and_is_recorded(): void
    {
        User::factory()->create(['username' => 'jane', 'password' => 'secret1234']);

        $this->from('/login')
            ->post('/login', ['login' => 'jane', 'password' => 'wrong-password'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('login');

        $this->assertGuest();
        $this->assertDatabaseHas('login_histories', ['login' => 'jane', 'successful' => false, 'failure_reason' => 'invalid_credentials']);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->inactive()->create(['username' => 'sleepy', 'password' => 'secret1234']);

        $this->post('/login', ['login' => 'sleepy', 'password' => 'secret1234'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_too_many_attempts(): void
    {
        User::factory()->create(['username' => 'jane', 'password' => 'secret1234']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', ['login' => 'jane', 'password' => 'wrong-password']);
        }

        $this->post('/login', ['login' => 'jane', 'password' => 'secret1234'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_successful_login_is_written_to_the_login_history(): void
    {
        $user = User::factory()->create(['username' => 'jane', 'password' => 'secret1234']);

        $this->post('/login', ['login' => 'jane', 'password' => 'secret1234']);

        $this->assertDatabaseHas('login_histories', ['user_id' => $user->id, 'successful' => true]);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_logout_closes_the_session_and_the_history_row(): void
    {
        $user = User::factory()->create(['username' => 'jane', 'password' => 'secret1234']);

        $this->post('/login', ['login' => 'jane', 'password' => 'secret1234']);
        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
        $this->assertNotNull(LoginHistory::query()->where('user_id', $user->id)->latest('id')->first()->logged_out_at);
    }

    public function test_forgot_password_screen_is_reachable(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
