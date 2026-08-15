<?php

/**
 * Graphical installer front controller (specification items 6 to 13).
 *
 * Served either through public/install.php or directly with
 * `php -S 127.0.0.1:8891 -t installer`.
 */

declare(strict_types=1);

require __DIR__.'/src/Installer.php';

use Nursing\Installer\Installer;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$basePath = dirname(__DIR__);
$installer = new Installer($basePath);

$state = $_SESSION['installer'] ?? [];
$step = isset($_GET['step']) ? (string) $_GET['step'] : 'welcome';

if ($installer->stepIndex($step) === 0 && $step !== 'welcome') {
    $step = 'welcome';
}

$errors = [];
$notice = null;
$test = null;
$result = null;

/*
 * The lock file makes a second run impossible (specification item 13). Only the
 * session that produced the lock may continue to the remaining steps; a query
 * parameter must never unlock the installer.
 */
if ($installer->isInstalled() && empty($state['installed'])) {
    $step = 'locked';
}

$post = static fn (string $key, string $default = ''): string => trim((string) ($_POST[$key] ?? $default));

if ($step !== 'locked' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    switch ($step) {
        case 'database':
            $state['database'] = [
                'host' => $post('host', '127.0.0.1'),
                'port' => $post('port', '3306'),
                'database' => $post('database'),
                'username' => $post('username', 'root'),
                // The field is rendered empty, so an empty submit keeps the password of the previous step.
                'password' => (string) ($_POST['password'] ?? '') !== ''
                    ? (string) $_POST['password']
                    : (string) ($state['database']['password'] ?? ''),
                'create_database' => isset($_POST['create_database']),
            ];

            if ($state['database']['database'] === '') {
                $errors[] = 'نام پایگاه داده الزامی است.';

                break;
            }

            $test = $installer->testDatabase($state['database']);

            if ($action === 'test') {
                break;
            }

            if (! $test['ok']) {
                $errors[] = $test['message'];
            }

            break;

        case 'organization':
            $state['organization'] = [
                'name' => $post('name'),
                'phone' => $post('phone'),
                'city' => $post('city'),
                'address' => $post('address'),
            ];

            if ($state['organization']['name'] === '') {
                $errors[] = 'نام سازمان الزامی است.';
            }

            break;

        case 'administrator':
            $state['administrator'] = [
                'name' => $post('name'),
                'username' => $post('username'),
                'mobile' => $post('mobile'),
                'email' => $post('email'),
                'password' => (string) ($_POST['password'] ?? ''),
            ];

            if ($state['administrator']['name'] === '') {
                $errors[] = 'نام و نام خانوادگی مدیر الزامی است.';
            }

            if (! preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $state['administrator']['username'])) {
                $errors[] = 'نام کاربری باید بین ۳ تا ۵۰ نویسه لاتین باشد.';
            }

            if (! preg_match('/^09\d{9}$/', $state['administrator']['mobile'])) {
                $errors[] = 'شماره موبایل باید ۱۱ رقم و با ۰۹ شروع شود.';
            }

            if ($state['administrator']['email'] !== '' && ! filter_var($state['administrator']['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'ایمیل معتبر نیست.';
            }

            if (strlen($state['administrator']['password']) < 8) {
                $errors[] = 'رمز عبور باید حداقل ۸ نویسه باشد.';
            }

            if ($state['administrator']['password'] !== (string) ($_POST['password_confirmation'] ?? '')) {
                $errors[] = 'تکرار رمز عبور مطابقت ندارد.';
            }

            break;

        case 'configuration':
            $state['configuration'] = [
                'app_name' => $post('app_name'),
                'app_url' => $post('app_url'),
                'locale' => $post('locale', 'fa'),
                'timezone' => $post('timezone', 'Asia/Tehran'),
                'primary_color' => $post('primary_color', '#0d9488'),
                'mail_mailer' => $post('mail_mailer', 'log'),
                'mail_from' => $post('mail_from'),
            ];

            if ($state['configuration']['app_name'] === '') {
                $errors[] = 'عنوان سامانه الزامی است.';
            }

            break;

        case 'installation':
            $result = $installer->install($state);

            if ($result['ok']) {
                /* From now on the credentials live in .env, not in the session file. */
                unset($state['administrator']['password'], $state['database']['password']);

                /* Lets this session (and only this session) reach the demo and complete steps. */
                $state['installed'] = true;
            }

            if (! $result['ok']) {
                $errors[] = 'نصب کامل نشد. جزئیات خطا در پایین آمده است.';
            }

            break;

        case 'demo':
            if ($action === 'install') {
                $demo = $installer->installDemoData();
                $notice = $demo['ok'] ? 'داده‌های نمونه ایجاد شد.' : 'ایجاد داده نمونه ناموفق بود: '.$demo['output'];

                if (! $demo['ok']) {
                    $errors[] = $notice;
                    $notice = null;
                }
            }

            break;
    }

    $_SESSION['installer'] = $state;

    /* The installation and demo steps show their own report instead of advancing. */
    $shouldAdvance = $errors === []
        && $action !== 'test'
        && ! in_array($step, ['installation', 'demo'], true);

    if ($shouldAdvance) {
        header('Location: ?step='.$installer->nextStep($step));
        exit;
    }
}

$view = __DIR__.'/views/'.($step === 'locked' ? 'locked' : $step).'.php';

require __DIR__.'/views/layout.php';
