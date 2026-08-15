<?php
/** @var array<string, mixed> $state @var array{ok: bool, message: string}|null $test */
$database = $state['database'] ?? ['host' => '127.0.0.1', 'port' => '3306', 'database' => 'nursing', 'username' => 'root', 'password' => '', 'create_database' => true];
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<h2 class="text-xl font-bold">اتصال به پایگاه داده</h2>
<p class="mt-2 text-sm text-slate-500">اطلاعات MySQL را وارد کنید. با دکمه «تست اتصال» می‌توانید صحت اطلاعات را بررسی کنید.</p>

<?php if ($test !== null): ?>
    <div class="mt-4 rounded-lg border p-3 text-sm <?= $test['ok'] ? 'border-teal-200 bg-teal-50 text-teal-700' : 'border-red-200 bg-red-50 text-red-700' ?>">
        <?= $e($test['message']) ?>
    </div>
<?php endif; ?>

<form method="post" class="mt-4 space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">میزبان</span>
            <input name="host" value="<?= $e($database['host']) ?>" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">پورت</span>
            <input name="port" value="<?= $e($database['port']) ?>" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">نام پایگاه داده</span>
            <input name="database" value="<?= $e($database['database']) ?>" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">نام کاربری</span>
            <input name="username" value="<?= $e($database['username']) ?>" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm sm:col-span-2">
            <span class="mb-1 block text-slate-600">رمز عبور</span>
            <input type="password" name="password" value="<?= $e($database['password']) ?>" class="w-full rounded-lg border px-3 py-2">
        </label>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="create_database" value="1" <?= ! empty($database['create_database']) ? 'checked' : '' ?>>
        اگر پایگاه داده وجود ندارد، ساخته شود
    </label>

    <div class="flex items-center justify-between">
        <a href="?step=requirements" class="text-sm text-slate-500 hover:underline">بازگشت</a>
        <div class="flex items-center gap-3">
            <button name="action" value="test" class="rounded-lg border px-4 py-2 text-sm">تست اتصال</button>
            <button name="action" value="next" class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">مرحله بعد</button>
        </div>
    </div>
</form>
