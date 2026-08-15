<?php
/** @var array{ok: bool, steps: list<array{title: string, ok: bool, output: string}>}|null $result */
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<h2 class="text-xl font-bold">اجرای نصب</h2>

<?php if ($result === null): ?>
    <p class="mt-3 leading-7 text-slate-600">
        با زدن دکمه زیر فایل <code>.env</code> ساخته می‌شود، کلید برنامه تولید می‌شود، جداول پایگاه داده ایجاد و
        داده‌های پایه (نقش‌ها، دسترسی‌ها، تنظیمات، سازمان و مدیر سیستم) ثبت می‌شوند و پیوند <code>storage</code> ساخته می‌شود.
    </p>
    <p class="mt-2 text-sm text-amber-700">توجه: جداول موجود در این پایگاه داده حذف و بازسازی می‌شوند.</p>

    <form method="post" class="mt-6 flex items-center justify-between">
        <a href="?step=configuration" class="text-sm text-slate-500 hover:underline">بازگشت</a>
        <button class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">شروع نصب</button>
    </form>
<?php else: ?>
    <div class="mt-4 space-y-3">
        <?php foreach ($result['steps'] as $item): ?>
            <div class="rounded-lg border p-3 text-sm <?= $item['ok'] ? 'border-teal-200 bg-teal-50' : 'border-red-200 bg-red-50' ?>">
                <div class="flex items-center justify-between">
                    <span><?= $e($item['title']) ?></span>
                    <span class="<?= $item['ok'] ? 'text-teal-700' : 'text-red-700' ?>"><?= $item['ok'] ? 'انجام شد' : 'ناموفق' ?></span>
                </div>
                <?php if ($item['output'] !== ''): ?>
                    <pre dir="ltr" class="mt-2 max-h-64 overflow-auto rounded bg-slate-900 p-3 text-xs text-slate-100"><?= $e($item['output']) ?></pre>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <form method="post"><button class="rounded-lg border px-4 py-2 text-sm">تلاش مجدد</button></form>
        <?php if ($result['ok']): ?>
            <a href="?step=demo" class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">مرحله بعد</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
