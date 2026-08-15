<?php

namespace Tests\Feature;

use App\Modules\Core\Console\Commands\InstallCommand;
use App\Modules\Core\Services\InstallerService;
use Illuminate\Support\Facades\Artisan;
use Nursing\Installer\Installer;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    private string $lockPath;

    protected function setUp(): void
    {
        parent::setUp();

        require_once base_path('installer/src/Installer.php');

        // An isolated lock file keeps the tests away from a real installation.
        config()->set('core.installer.lock_file', sys_get_temp_dir().'/nursing-test-'.uniqid().'.lock');

        $this->lockPath = app(InstallerService::class)->lockPath();
    }

    protected function tearDown(): void
    {
        if (is_file($this->lockPath)) {
            unlink($this->lockPath);
        }

        parent::tearDown();
    }

    public function test_visitors_are_redirected_to_the_installer_while_the_lock_file_is_missing(): void
    {
        config()->set('core.installer.enabled', true);

        $this->assertFalse(app(InstallerService::class)->isInstalled());

        $this->get('/login')->assertRedirect('/install.php');
    }

    public function test_the_application_is_reachable_once_the_lock_file_exists(): void
    {
        config()->set('core.installer.enabled', true);
        file_put_contents($this->lockPath, json_encode(['version' => '1.0.0', 'installed_at' => now()->toIso8601String()]));

        $this->assertTrue(app(InstallerService::class)->isInstalled());
        $this->assertSame('1.0.0', app(InstallerService::class)->lockContents()['version']);

        $this->get('/login')->assertOk();
    }

    public function test_the_installer_exposes_every_required_step(): void
    {
        $steps = array_column(Installer::STEPS, 'key');

        $this->assertSame([
            'welcome',
            'requirements',
            'database',
            'organization',
            'administrator',
            'configuration',
            'installation',
            'demo',
            'complete',
        ], $steps);

        foreach ($steps as $step) {
            $this->assertFileExists(base_path('installer/views/'.$step.'.php'));
        }

        $this->assertFileExists(base_path('public/install.php'));
    }

    public function test_requirement_checks_pass_on_this_environment(): void
    {
        $requirements = (new Installer(base_path()))->requirements();

        $this->assertTrue($requirements['php']['passed']);
        $this->assertTrue($requirements['passed'], 'requirements report a failure: '.json_encode($requirements));
    }

    public function test_the_database_connection_test_reports_a_failure_for_wrong_credentials(): void
    {
        $result = (new Installer(base_path()))->testDatabase([
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'nursing_does_not_exist',
            'username' => 'invalid-user',
            'password' => 'invalid-password',
        ]);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('خطای اتصال', $result['message']);
    }

    public function test_the_environment_file_is_generated_with_an_application_key(): void
    {
        $installer = new Installer(sys_get_temp_dir().'/nursing-installer-'.uniqid());
        mkdir($installer->basePath(), 0755, true);
        file_put_contents($installer->basePath('.env.example'), "APP_NAME=Laravel\nAPP_KEY=\nDB_DATABASE=laravel\n");

        $installer->writeEnvironmentFile([
            'configuration' => ['app_name' => 'Nursing', 'app_url' => 'http://localhost', 'timezone' => 'Asia/Tehran'],
            'database' => ['host' => '127.0.0.1', 'port' => '3306', 'database' => 'nursing_db', 'username' => 'root', 'password' => 'secret'],
        ]);

        $env = (string) file_get_contents($installer->basePath('.env'));

        $this->assertMatchesRegularExpression('/^APP_KEY=base64:.+$/m', $env);
        $this->assertStringContainsString('DB_DATABASE=nursing_db', $env);
        $this->assertStringContainsString('DB_PASSWORD=secret', $env);
        $this->assertStringContainsString('APP_TIMEZONE=Asia/Tehran', $env);

        unlink($installer->basePath('.env'));
        unlink($installer->basePath('.env.example'));
        rmdir($installer->basePath());
    }

    public function test_the_administrator_password_never_reaches_the_command_line(): void
    {
        $installer = new Installer(base_path());

        $method = new \ReflectionMethod($installer, 'installOptions');
        $options = $method->invoke($installer, [
            'organization' => ['name' => 'سازمان'],
            'administrator' => ['username' => 'admin', 'password' => 'super-secret-password'],
        ]);

        $this->assertContains('--admin-username=admin', $options);
        $this->assertStringNotContainsString('super-secret-password', implode(' ', $options));
    }

    public function test_the_install_command_reads_the_password_from_the_environment(): void
    {
        putenv(InstallCommand::PASSWORD_ENV.'=environment-password');

        try {
            $command = app(InstallCommand::class);
            $password = (new \ReflectionMethod($command, 'adminPassword'))->invoke($command);
        } finally {
            putenv(InstallCommand::PASSWORD_ENV);
        }

        $this->assertSame('environment-password', $password);
    }

    public function test_the_install_command_is_registered(): void
    {
        $this->assertArrayHasKey('nursing:install', Artisan::all());
    }
}
