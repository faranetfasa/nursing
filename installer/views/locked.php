<?php
/** @var \Nursing\Installer\Installer $installer */
$lock = $installer->lockPath();
$data = json_decode((string) @file_get_contents($lock), true) ?: [];
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<h2 class="text-xl font-bold">سامانه قبلاً نصب شده است</h2>
<p class="mt-3 leading-7 text-slate-600">
    فایل قفل نصب وجود دارد، بنابراین نصاب اجرا نمی‌شود. برای نصب مجدد، فایل زیر را حذف کنید:
</p>
<pre dir="ltr" class="mt-3 rounded bg-slate-900 p-3 text-xs text-slate-100"><?= $e($lock) ?></pre>

<?php if (! empty($data['installed_at'])): ?>
    <p class="mt-3 text-sm text-slate-500">زمان نصب: <code dir="ltr"><?= $e($data['installed_at']) ?></code></p>
<?php endif; ?>

<div class="mt-6 flex justify-end">
    <a href="../login" class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">ورود به سامانه</a>
</div>
