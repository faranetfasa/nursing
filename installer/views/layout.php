<?php
$statusIcon = static fn (string $status): string => match ($status) {
    'pass' => '✓',
    'fail' => '✗',
    default => '⚠',
};
$nextStep = $steps[min(array_search($step, $steps, true) + 1, count($steps) - 1)];
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$stepUrl = static function (string $target) use ($escape): string {
    $query = ['step' => $target];
    if (isset($_GET['token']) && is_string($_GET['token']) && $_GET['token'] !== '') {
        $query['token'] = $_GET['token'];
    }

    return $escape('?' . http_build_query($query));
};
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>نصب سامانه جامع سلامت</title>
    <link rel="stylesheet" href="assets/installer.css">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand">سامانه جامع سلامت</div>
        <?php foreach ($steps as $index => $item): ?>
            <a class="step <?= $item === $step ? 'active' : '' ?>" href="<?= $stepUrl($item) ?>">
                <span><?= $index + 1 ?></span><?= $escape(ucfirst($item)) ?>
            </a>
        <?php endforeach; ?>
    </aside>
    <main class="panel">
        <?php if ($step === 'welcome'): ?>
            <h1>به نصب سامانه جامع مدیریت خدمات سلامت و پرستاری خوش آمدید</h1>
            <p>این Wizard نصب روی XAMPP، تنظیم دیتابیس، سازمان، مدیر اصلی و تنظیمات اولیه را هدایت می‌کند.</p>
        <?php elseif ($step === 'requirements'): ?>
            <h1>بررسی سیستم</h1>
            <div class="grid">
                <?php foreach ($requirements as $item): ?>
                    <div class="check <?= $escape($item['status']) ?>">
                        <strong><?= $statusIcon($item['status']) ?> <?= $escape($item['name']) ?></strong>
                        <small><?= $escape($item['message']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h1><?= $escape(ucfirst($step)) ?></h1>
            <p>فرم این مرحله در فاز تکمیل Installer به سرویس‌های Laravel، Migration Runner و Environment Writer متصل می‌شود.</p>
            <div class="placeholder">آماده برای پیاده‌سازی Production در فاز بعدی</div>
        <?php endif; ?>
        <div class="actions">
            <a class="button" href="<?= $stepUrl($nextStep) ?>">ادامه</a>
            <a class="button secondary" href="<?= $stepUrl('requirements') ?>">بررسی مجدد</a>
        </div>
    </main>
</div>
</body>
</html>
