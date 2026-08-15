<?php

namespace Nursing\Installer;

/**
 * Standalone installer engine. It must run before Laravel is installable, so it
 * only relies on plain PHP: requirement checks, database connection test, .env
 * generation and delegation of the database work to "php artisan nursing:install".
 */
class Installer
{
    /** @var list<array{key: string, title: string, description: string}> */
    public const STEPS = [
        ['key' => 'welcome', 'title' => 'خوش‌آمدید', 'description' => 'شروع نصب سامانه'],
        ['key' => 'requirements', 'title' => 'بررسی سیستم', 'description' => 'نسخه PHP و افزونه‌های موردنیاز'],
        ['key' => 'database', 'title' => 'پایگاه داده', 'description' => 'اتصال به MySQL'],
        ['key' => 'organization', 'title' => 'سازمان', 'description' => 'اطلاعات سازمان'],
        ['key' => 'administrator', 'title' => 'مدیر سیستم', 'description' => 'حساب مدیر ارشد'],
        ['key' => 'configuration', 'title' => 'تنظیمات', 'description' => 'آدرس، زبان و ظاهر'],
        ['key' => 'installation', 'title' => 'نصب', 'description' => 'اجرای مهاجرت و داده‌های پایه'],
        ['key' => 'demo', 'title' => 'داده نمونه', 'description' => 'ایجاد داده‌های آزمایشی'],
        ['key' => 'complete', 'title' => 'پایان', 'description' => 'ورود به سامانه'],
    ];

    public function __construct(private readonly string $basePath) {}

    public function basePath(string $path = ''): string
    {
        return rtrim($this->basePath, DIRECTORY_SEPARATOR).($path === '' ? '' : DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR));
    }

    public function lockPath(): string
    {
        return $this->basePath('storage/installed.lock');
    }

    public function isInstalled(): bool
    {
        return is_file($this->lockPath());
    }

    /* ---------------------------------------------------------------- steps */

    public function stepIndex(string $key): int
    {
        foreach (self::STEPS as $index => $step) {
            if ($step['key'] === $key) {
                return $index;
            }
        }

        return 0;
    }

    public function nextStep(string $key): string
    {
        $next = self::STEPS[$this->stepIndex($key) + 1] ?? self::STEPS[array_key_last(self::STEPS)];

        return $next['key'];
    }

    /* --------------------------------------------------------- requirements */

    /** @return array{php: array<string, mixed>, extensions: list<array<string, mixed>>, permissions: list<array<string, mixed>>, passed: bool} */
    public function requirements(): array
    {
        $minimum = '8.2.0';

        $php = [
            'title' => 'نسخه PHP (حداقل '.$minimum.')',
            'current' => PHP_VERSION,
            'passed' => version_compare(PHP_VERSION, $minimum, '>='),
        ];

        $extensions = [];

        foreach (['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'json', 'curl', 'fileinfo', 'ctype', 'xml', 'bcmath', 'zip', 'gd'] as $extension) {
            $extensions[] = [
                'title' => 'افزونه '.$extension,
                'passed' => extension_loaded($extension),
                'optional' => in_array($extension, ['zip', 'gd', 'bcmath'], true),
            ];
        }

        $permissions = [];

        foreach (['storage', 'storage/framework', 'storage/logs', 'bootstrap/cache', '.'] as $path) {
            $full = $this->basePath($path);
            $permissions[] = [
                'title' => 'قابلیت نوشتن در '.($path === '.' ? 'ریشه پروژه' : $path),
                'passed' => is_dir($full) && is_writable($full),
            ];
        }

        $passed = $php['passed'];

        foreach (array_merge($extensions, $permissions) as $item) {
            if (! $item['passed'] && ! ($item['optional'] ?? false)) {
                $passed = false;
            }
        }

        return ['php' => $php, 'extensions' => $extensions, 'permissions' => $permissions, 'passed' => $passed];
    }

    /* ------------------------------------------------------------- database */

    /** @param array<string, string> $config @return array{ok: bool, message: string} */
    public function testDatabase(array $config): array
    {
        $host = $config['host'] ?: '127.0.0.1';
        $port = $config['port'] ?: '3306';
        $database = $config['database'] ?? '';

        try {
            $pdo = new \PDO(
                sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $host, $port),
                $config['username'] ?? 'root',
                $config['password'] ?? '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );

            $version = (string) $pdo->query('select version()')->fetchColumn();

            $exists = (bool) $pdo->query('show databases like '.$pdo->quote($database))->fetchColumn();

            if (! $exists) {
                if (empty($config['create_database'])) {
                    return ['ok' => false, 'message' => sprintf('اتصال برقرار شد (MySQL %s) اما پایگاه داده «%s» وجود ندارد.', $version, $database)];
                }

                $pdo->exec(sprintf('create database `%s` character set utf8mb4 collate utf8mb4_unicode_ci', str_replace('`', '', $database)));

                return ['ok' => true, 'message' => sprintf('اتصال برقرار شد (MySQL %s) و پایگاه داده «%s» ساخته شد.', $version, $database)];
            }

            return ['ok' => true, 'message' => sprintf('اتصال با موفقیت برقرار شد (MySQL %s).', $version)];
        } catch (\Throwable $exception) {
            return ['ok' => false, 'message' => 'خطای اتصال: '.$exception->getMessage()];
        }
    }

    /* ------------------------------------------------------------------ env */

    /** @param array<string, mixed> $state */
    public function writeEnvironmentFile(array $state): void
    {
        $example = $this->basePath('.env.example');
        $target = $this->basePath('.env');

        $contents = is_file($example) ? (string) file_get_contents($example) : '';

        $values = [
            'APP_NAME' => $state['configuration']['app_name'] ?? 'Nursing',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => rtrim((string) ($state['configuration']['app_url'] ?? ''), '/'),
            'APP_LOCALE' => $state['configuration']['locale'] ?? 'fa',
            'APP_TIMEZONE' => $state['configuration']['timezone'] ?? 'Asia/Tehran',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $state['database']['host'] ?? '127.0.0.1',
            'DB_PORT' => $state['database']['port'] ?? '3306',
            'DB_DATABASE' => $state['database']['database'] ?? '',
            'DB_USERNAME' => $state['database']['username'] ?? 'root',
            'DB_PASSWORD' => $state['database']['password'] ?? '',
            'MAIL_MAILER' => $state['configuration']['mail_mailer'] ?? 'log',
            'MAIL_FROM_ADDRESS' => $state['configuration']['mail_from'] ?? 'no-reply@example.com',
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'database',
            'QUEUE_CONNECTION' => 'database',
            'INSTALLER_ENABLED' => 'true',
        ];

        foreach ($values as $key => $value) {
            $contents = $this->setEnvValue($contents, $key, (string) $value);
        }

        $contents = $this->setEnvValue($contents, 'APP_KEY', $this->generateKey());

        file_put_contents($target, $contents);
    }

    public function generateKey(): string
    {
        return 'base64:'.base64_encode(random_bytes(32));
    }

    private function setEnvValue(string $contents, string $key, string $value): string
    {
        $quoted = preg_match('/\s|#|"/', $value) === 1 ? '"'.str_replace('"', '\"', $value).'"' : $value;
        $line = $key.'='.$quoted;

        if (preg_match('/^'.preg_quote($key, '/').'=.*$/m', $contents) === 1) {
            return (string) preg_replace('/^'.preg_quote($key, '/').'=.*$/m', $line, $contents);
        }

        return rtrim($contents, "\n")."\n".$line."\n";
    }

    /* -------------------------------------------------------------- artisan */

    /** @param list<string> $arguments @return array{ok: bool, output: string} */
    public function artisan(array $arguments): array
    {
        $php = $this->phpBinary();
        $command = array_merge([$php, $this->basePath('artisan')], $arguments);

        $escaped = implode(' ', array_map(static fn (string $part): string => escapeshellarg($part), $command)).' 2>&1';

        $output = [];
        $status = 1;
        exec($escaped, $output, $status);

        return ['ok' => $status === 0, 'output' => implode("\n", $output)];
    }

    public function phpBinary(): string
    {
        $candidate = PHP_BINARY;

        if (str_contains(strtolower(basename($candidate)), 'php')) {
            return $candidate;
        }

        return (string) (PHP_BINDIR.DIRECTORY_SEPARATOR.'php'.(DIRECTORY_SEPARATOR === '\\' ? '.exe' : ''));
    }

    /** @param array<string, mixed> $state @return array{ok: bool, steps: list<array{title: string, ok: bool, output: string}>} */
    public function install(array $state): array
    {
        $steps = [];

        $this->writeEnvironmentFile($state);
        $steps[] = ['title' => 'ساخت فایل .env و کلید برنامه', 'ok' => is_file($this->basePath('.env')), 'output' => ''];

        $result = $this->artisan(array_merge([
            'nursing:install',
            '--force',
            '--fresh',
            '--no-interaction',
        ], $this->installOptions($state)));

        $steps[] = ['title' => 'مهاجرت‌ها، داده‌های پایه، مدیر سیستم و پیوند storage', 'ok' => $result['ok'], 'output' => $result['output']];

        return ['ok' => ! in_array(false, array_column($steps, 'ok'), true), 'steps' => $steps];
    }

    /** @param array<string, mixed> $state @return list<string> */
    private function installOptions(array $state): array
    {
        $map = [
            '--org-name' => $state['organization']['name'] ?? '',
            '--org-phone' => $state['organization']['phone'] ?? '',
            '--org-city' => $state['organization']['city'] ?? '',
            '--org-address' => $state['organization']['address'] ?? '',
            '--app-name' => $state['configuration']['app_name'] ?? '',
            '--primary-color' => $state['configuration']['primary_color'] ?? '',
            '--timezone' => $state['configuration']['timezone'] ?? '',
            '--admin-name' => $state['administrator']['name'] ?? '',
            '--admin-username' => $state['administrator']['username'] ?? '',
            '--admin-mobile' => $state['administrator']['mobile'] ?? '',
            '--admin-email' => $state['administrator']['email'] ?? '',
            '--admin-password' => $state['administrator']['password'] ?? '',
        ];

        $options = [];

        foreach ($map as $option => $value) {
            if ((string) $value !== '') {
                $options[] = $option.'='.$value;
            }
        }

        return $options;
    }

    /** @return array{ok: bool, output: string} */
    public function installDemoData(): array
    {
        return $this->artisan(['db:seed', '--class=App\\Modules\\Core\\Database\\Seeders\\DemoDataSeeder', '--force', '--no-interaction']);
    }
}
