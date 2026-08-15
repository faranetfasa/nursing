# نصب روی XAMPP

## پیش‌نیازها

- XAMPP با PHP 8.2 یا بالاتر
- MySQL 8 یا بالاتر (MariaDB 10.6+ نیز کار می‌کند)
- Composer 2
- Node.js 20+ و npm (فقط برای ساخت فایل‌های frontend)
- افزونه‌های PHP: `pdo`, `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `json`, `curl`,
  `fileinfo`, `ctype`, `xml`, `bcmath`, `zip`, `gd`

نصاب همه این موارد را در مرحله «بررسی سیستم» کنترل می‌کند.

## ۱) قرار دادن پروژه

```bash
cd C:\xampp\htdocs
git clone https://github.com/faranetfasa/nursing.git
cd nursing
composer install
npm install
npm run build
```

## ۲) دسترسی نوشتن

مطمئن شوید این مسیرها قابل نوشتن هستند: `storage/`, `storage/framework/*`, `storage/logs/`,
`bootstrap/cache/` و ریشه پروژه (برای ساخت `.env`).

در لینوکس/مک:

```bash
chmod -R 775 storage bootstrap/cache
```

## ۳) ساخت پایگاه داده (اختیاری)

نصاب می‌تواند خودش پایگاه داده را بسازد. اگر می‌خواهید دستی بسازید، در phpMyAdmin یک دیتابیس با
Collation برابر `utf8mb4_unicode_ci` ایجاد کنید.

## ۴) اجرای نصاب گرافیکی

Apache و MySQL را از کنترل‌پنل XAMPP اجرا کنید و این آدرس را باز کنید:

```
http://localhost/nursing/public/install.php
```

مراحل نصاب:

1. **خوش‌آمدید**
2. **بررسی سیستم** — نسخه PHP، افزونه‌ها و دسترسی نوشتن
3. **پایگاه داده** — با دکمه «تست اتصال» و امکان ساخت خودکار دیتابیس
4. **سازمان** — نام، تلفن، شهر و آدرس سازمان
5. **مدیر سیستم** — نام، username، موبایل، ایمیل و رمز عبور مدیر ارشد
6. **تنظیمات** — عنوان برنامه، آدرس، زبان، منطقه زمانی، رنگ سازمانی و ایمیل
7. **نصب** — ساخت `.env`، تولید `APP_KEY`، اجرای Migration و Seed، `storage:link` و ساخت `installed.lock`
8. **داده نمونه** — ایجاد شعبه/دپارتمان/کاربران آزمایشی (اختیاری)
9. **پایان** — لینک ورود به سامانه

پس از پایان، فایل `storage/installed.lock` ساخته می‌شود و اجرای دوباره نصاب قفل می‌شود. برای نصب مجدد
این فایل را حذف کنید (تمام داده‌ها با گزینه «نصب تازه» پاک می‌شوند).

اگر `installed.lock` وجود نداشته باشد، میان‌افزار `EnsureApplicationIsInstalled` هر درخواست را به نصاب
هدایت می‌کند (درخواست‌های JSON پاسخ 503 می‌گیرند).

## ۵) ورود به سامانه

```
http://localhost/nursing/public/login
```

با username و رمزی که در مرحله «مدیر سیستم» تعیین کردید وارد شوید.

## نصب از خط فرمان (جایگزین نصاب)

```bash
cp .env.example .env
php artisan key:generate
php artisan nursing:install --fresh --demo \
  --org-name="موسسه نمونه" \
  --admin-name="مدیر سیستم" \
  --admin-username=admin \
  --admin-mobile=09120000000 \
  --admin-email=admin@example.com \
  --admin-password=Secret12345
```

## عیب‌یابی

| مشکل | راه‌حل |
| --- | --- |
| `Vite manifest not found` | `npm install && npm run build` را اجرا کنید |
| خطای اتصال دیتابیس | مقادیر `DB_*` در `.env` و اجرای MySQL را بررسی کنید |
| صفحه سفید / خطای 500 | `storage/logs/laravel.log` را ببینید؛ Error ID نمایش‌داده‌شده در همان لاگ قابل جست‌وجو است |
| نصاب باز نمی‌شود | وجود `public/install.php` و دسترسی نوشتن روی ریشه پروژه را بررسی کنید |
| بعد از نصب همه صفحات به نصاب می‌روند | `storage/installed.lock` ساخته نشده است؛ دسترسی نوشتن `storage/` را بررسی کنید |
