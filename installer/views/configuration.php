<?php
/** @var array<string, mixed> $state */
$defaultUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http').'://'.($_SERVER['HTTP_HOST'] ?? 'localhost');
$configuration = $state['configuration'] ?? [
    'app_name' => 'سامانه مدیریت خدمات پرستاری',
    'app_url' => $defaultUrl,
    'locale' => 'fa',
    'timezone' => 'Asia/Tehran',
    'primary_color' => '#0d9488',
    'mail_mailer' => 'log',
    'mail_from' => '',
];
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<h2 class="text-xl font-bold">تنظیمات سامانه</h2>
<p class="mt-2 text-sm text-slate-500">همه این مقادیر بعد از نصب از بخش تنظیمات قابل تغییر هستند.</p>

<form method="post" class="mt-4 space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">عنوان سامانه *</span>
            <input name="app_name" value="<?= $e($configuration['app_name']) ?>" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">آدرس سامانه</span>
            <input name="app_url" value="<?= $e($configuration['app_url']) ?>" dir="ltr" class="w-full rounded-lg border px-3 py-2">
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">زبان</span>
            <select name="locale" class="w-full rounded-lg border px-3 py-2">
                <option value="fa" <?= $configuration['locale'] === 'fa' ? 'selected' : '' ?>>فارسی</option>
                <option value="en" <?= $configuration['locale'] === 'en' ? 'selected' : '' ?>>English</option>
            </select>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">منطقه زمانی</span>
            <input name="timezone" value="<?= $e($configuration['timezone']) ?>" dir="ltr" class="w-full rounded-lg border px-3 py-2">
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">رنگ سازمانی</span>
            <input type="color" name="primary_color" value="<?= $e($configuration['primary_color']) ?>" class="h-10 w-full rounded-lg border px-2">
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">روش ارسال ایمیل</span>
            <select name="mail_mailer" class="w-full rounded-lg border px-3 py-2">
                <option value="log" <?= $configuration['mail_mailer'] === 'log' ? 'selected' : '' ?>>ثبت در لاگ</option>
                <option value="smtp" <?= $configuration['mail_mailer'] === 'smtp' ? 'selected' : '' ?>>SMTP</option>
            </select>
        </label>
        <label class="block text-sm sm:col-span-2">
            <span class="mb-1 block text-slate-600">ایمیل فرستنده</span>
            <input type="email" name="mail_from" value="<?= $e($configuration['mail_from']) ?>" dir="ltr" class="w-full rounded-lg border px-3 py-2">
        </label>
    </div>

    <div class="flex items-center justify-between">
        <a href="?step=administrator" class="text-sm text-slate-500 hover:underline">بازگشت</a>
        <button class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">مرحله بعد</button>
    </div>
</form>
