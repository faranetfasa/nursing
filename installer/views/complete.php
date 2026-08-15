<?php
/** @var array<string, mixed> $state @var \Nursing\Installer\Installer $installer */
$admin = $state['administrator'] ?? [];
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$appUrl = rtrim((string) ($state['configuration']['app_url'] ?? ''), '/');
unset($_SESSION['installer']);
?>
<h2 class="text-xl font-bold text-teal-700">نصب با موفقیت انجام شد</h2>
<p class="mt-3 leading-7 text-slate-600">
    سامانه آماده استفاده است. فایل <code>storage/installed.lock</code> ساخته شده و نصاب دیگر اجرا نمی‌شود.
</p>

<div class="mt-4 rounded-lg border p-4 text-sm">
    <div class="flex justify-between border-b py-2"><span class="text-slate-500">نام کاربری مدیر</span><code dir="ltr"><?= $e($admin['username'] ?? 'admin') ?></code></div>
    <div class="flex justify-between py-2"><span class="text-slate-500">آدرس ورود</span><code dir="ltr"><?= $e($appUrl.'/login') ?></code></div>
</div>

<div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
    برای امنیت بیشتر پوشه <code>installer/</code> و فایل <code>public/install.php</code> را از سرور حذف کنید.
</div>

<div class="mt-6 flex justify-end">
    <a href="<?= $e(($appUrl !== '' ? $appUrl : '.').'/login') ?>" class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">ورود به سامانه</a>
</div>
