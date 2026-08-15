<?php
/**
 * @var \Nursing\Installer\Installer $installer
 * @var string $step
 * @var string $view
 * @var array<int, string> $errors
 * @var string|null $notice
 */
$currentIndex = $installer->stepIndex($step);
$e = static fn (?string $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>نصب سامانه مدیریت خدمات پرستاری</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Vazirmatn, Tahoma, sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="mx-auto max-w-5xl p-6">
    <header class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-teal-700">نصب سامانه مدیریت خدمات پرستاری</h1>
        <p class="mt-1 text-sm text-slate-500">نصب گام‌به‌گام روی XAMPP یا هر سرور PHP</p>
    </header>

    <div class="grid gap-6 md:grid-cols-4">
        <aside class="rounded-xl bg-white p-4 shadow-sm md:col-span-1">
            <ol class="space-y-2 text-sm">
                <?php foreach (\Nursing\Installer\Installer::STEPS as $index => $item): ?>
                    <li class="flex items-center gap-2 rounded-lg px-2 py-1 <?= $index === $currentIndex ? 'bg-teal-50 font-semibold text-teal-700' : ($index < $currentIndex ? 'text-teal-600' : 'text-slate-400') ?>">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full border text-xs <?= $index <= $currentIndex ? 'border-teal-500' : 'border-slate-300' ?>"><?= $index + 1 ?></span>
                        <span><?= $e($item['title']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </aside>

        <main class="rounded-xl bg-white p-6 shadow-sm md:col-span-3">
            <?php if (! empty($errors)): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (! empty($notice)): ?>
                <div class="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-4 text-sm text-teal-700"><?= $e($notice) ?></div>
            <?php endif; ?>

            <?php require $view; ?>
        </main>
    </div>

    <footer class="mt-6 text-center text-xs text-slate-400">
        نسخه ۱.۰.۰ &mdash; فاز Foundation
    </footer>
</div>
</body>
</html>
