<?php
/** @var array<string, mixed> $state */
$organization = $state['organization'] ?? ['name' => '', 'phone' => '', 'city' => '', 'address' => ''];
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<h2 class="text-xl font-bold">اطلاعات سازمان</h2>
<p class="mt-2 text-sm text-slate-500">این اطلاعات به‌عنوان سازمان اصلی و شعبه مرکزی ثبت می‌شود و بعداً قابل ویرایش است.</p>

<form method="post" class="mt-4 space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm sm:col-span-2">
            <span class="mb-1 block text-slate-600">نام سازمان *</span>
            <input name="name" value="<?= $e($organization['name']) ?>" class="w-full rounded-lg border px-3 py-2" required>
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">تلفن</span>
            <input name="phone" value="<?= $e($organization['phone']) ?>" class="w-full rounded-lg border px-3 py-2">
        </label>
        <label class="block text-sm">
            <span class="mb-1 block text-slate-600">شهر</span>
            <input name="city" value="<?= $e($organization['city']) ?>" class="w-full rounded-lg border px-3 py-2">
        </label>
        <label class="block text-sm sm:col-span-2">
            <span class="mb-1 block text-slate-600">نشانی</span>
            <textarea name="address" rows="2" class="w-full rounded-lg border px-3 py-2"><?= $e($organization['address']) ?></textarea>
        </label>
    </div>

    <div class="flex items-center justify-between">
        <a href="?step=database" class="text-sm text-slate-500 hover:underline">بازگشت</a>
        <button class="rounded-lg bg-teal-600 px-5 py-2 text-white hover:bg-teal-700">مرحله بعد</button>
    </div>
</form>
