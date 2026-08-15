<?php

declare(strict_types=1);

require_once __DIR__ . '/services/InstallLock.php';
require_once __DIR__ . '/services/InstallerAccessGuard.php';
require_once __DIR__ . '/checks/RequirementChecker.php';

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');
header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");

$guard = new InstallerAccessGuard(__DIR__ . '/storage/installer_token.txt');
if (! $guard->isAllowed($_SERVER['REMOTE_ADDR'] ?? null, $_GET['token'] ?? null)) {
    http_response_code(403);
    echo '<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>Installer Forbidden</title><body style="font-family:tahoma;padding:3rem;background:#f8fafc"><h1>دسترسی به نصب مجاز نیست</h1><p>نصب فقط از سرور محلی یا با Access Token معتبر امکان‌پذیر است.</p></body></html>';
    exit;
}

$lock = new InstallLock(__DIR__ . '/storage/installed.lock');
if ($lock->isLocked()) {
    http_response_code(403);
    echo '<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>Installer Locked</title><body style="font-family:tahoma;padding:3rem;background:#f8fafc"><h1>نصب قبلاً انجام شده است</h1><p>برای Repair یا Update باید از پنل مدیر اصلی اقدام شود.</p></body></html>';
    exit;
}

$requestedStep = $_GET['step'] ?? 'welcome';
$steps = ['welcome','requirements','database','organization','administrator','configuration','installation','complete'];
$step = is_string($requestedStep) && in_array($requestedStep, $steps, true) ? $requestedStep : 'welcome';

$checker = new RequirementChecker();
$requirements = $checker->check();

include __DIR__ . '/views/layout.php';
