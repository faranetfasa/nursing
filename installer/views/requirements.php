<?php
/** @var \Nursing\Installer\Installer $installer */
$requirements = $installer->requirements();
$badge = static fn (bool $ok, bool $optional = false): string => $ok
    ? '<span class="rounded-full bg-teal-50 px-2 py-1 text-xs text-teal-700">تأیید</span>'
    : ($optional
        ? '<span class="rounded-full bg-amber-50 px-2 py-1 text-xs text-amber-700">اختیاری</span>'
        : '<span class="rounded-full bg-red-50 px-2 py-1 text-xs text-red-700">ناموجود</span>');
?>
<h2 class="text-xl font-bold">بررسی پیش‌نیازهای سیستم</h2>

<div class="mt-4 space-y-4">
    <div class="rounded-lg border p-4">
        <div class="flex items-center justify-between text-sm">
            <span><?= htmlspecialchars($requirements['php']['title']) ?></span>
            <span class="flex items-center gap-2">
                <code class="text-slate-500"><?= htmlspecialchars($requirements['php']['current']) ?></code>
                <?= $badge((bool) $requirements['php']['passed']) ?>
            </span>
        </div>
    </div>

    <div class="rounded-lg border p-4">
        <h3 class="mb-2 text-sm font-semibold">افزونه‌های PHP</h3>
        <div class="grid gap-2 text-sm sm:grid-cols-2">
            <?php foreach ($requirements['extensions'] as $item): ?>
                <div class="flex items-center justify-between gap-2">
                    <span><?= htmlspecialchars($item['title']) ?></span>
                    <?= $badge((bool) $item['passed'], (bool) ($item['optional'] ?? false)) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="rounded-lg border p-4">
        <h3 class="mb-2 text-sm font-semibold">دسترسی پوشه‌ها</h3>
        <div class="space-y-2 text-sm">
            <?php foreach ($requirements['permissions'] as $item): ?>
                <div class="flex items-center justify-between gap-2">
                    <span><?= htmlspecialchars($item['title']) ?></span>
                    <?= $badge((bool) $item['passed']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<form method="post" class="mt-6 flex items-center justify-between">
    <a href="?step=welcome" class="text-sm text-slate-500 hover:underline">بازگشت</a>
    <div class="flex items-center gap-3">
        <a href="?step=requirements" class="rounded-lg border px-4 py-2 text-sm">بررسی مجدد</a>
        <button <?= $requirements['passed'] ? '' : 'disabled' ?> class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:bg-slate-300">مرحله بعد</button>
    </div>
</form>
