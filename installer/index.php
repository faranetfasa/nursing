<?php

declare(strict_types=1);

require_once __DIR__ . '/services/ErrorHandler.php';
require_once __DIR__ . '/services/InstallLock.php';
require_once __DIR__ . '/checks/RequirementChecker.php';

ErrorHandler::register();

$lock = new InstallLock(__DIR__ . '/storage/installed.lock');
if ($lock->isLocked()) {
    http_response_code(403);
    echo '<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>Installer Locked</title><body style="font-family:tahoma;padding:3rem;background:#f8fafc"><h1>نصب قبلاً انجام شده است</h1><p>برای Repair یا Update باید از پنل مدیر اصلی اقدام شود.</p></body></html>';
    exit;
}

$steps = ['welcome','requirements','database','organization','administrator','configuration','installation','complete'];
$step = $_GET['step'] ?? 'welcome';
if (! is_string($step) || ! in_array($step, $steps, true)) {
    $step = 'welcome';
}

$checker = new RequirementChecker();
$requirements = $checker->check();

include __DIR__ . '/views/layout.php';
