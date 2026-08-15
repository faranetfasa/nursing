<?php
/** @var array<string, mixed> $state */
$admin = $state['administrator'] ?? ['name' => '', 'username' => 'admin', 'mobile' => '', 'email' => '', 'password' => ''];
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<h2 class="text-xl font-bold">حساب مدیر ارشد</h2>
<p class="mt-2 text-sm text-slate-500">این حساب نقش «مدیر ارشد سیستم» را می‌گیرد و به همه بخش‌ها دسترسی دارد.</p>

<form method="post" class="mt-4 space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">نام و نام خانوادگی *</span>
            <input name="name" value="<?= $e($admin['name']) ?>" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">نام کاربری *</span>
            <input name="username" value="<?= $e($admin['username']) ?>" dir="ltr" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">موبایل *</span>
            <input name="mobile" value="<?= $e($admin['mobile']) ?>" dir="ltr" placeholder="09121234567" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">ایمیل</span>
            <input type="email" name="email" value="<?= $e($admin['email']) ?>" dir="ltr" class="w-full rounded-lg border px-3 py-2">
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">رمز عبور * (حداقل ۸ نویسه)</span>
            <input type="password" name="password" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">تکرار رمز عبور *</span>
            <input type="password" name="password_confirmation" class="w-full rounded-lg border px-3 py-2" required>
        </label>
    </div>

    <div class="flex items-center justify-between">
        <a href="?step=organization" class="text-sm text-slate-500 hover:underline">بازگشت</a>
        <button class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">مرحله بعد</button>
    </div>
</form>
