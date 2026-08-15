# سامانه مدیریت خدمات پرستاری

زیرساخت ماژولار Laravel برای سامانه مدیریت خدمات پرستاری. این مخزن خروجی **فاز Foundation** است: اسکلت معماری، ماژول `Core` (احتیاجات مشترک)، نصاب گرافیکی و رابط کاربری فارسی (RTL) آماده است و ماژول‌های دامنه در فازهای بعدی روی همین ساخت اضافه می‌شوند.

- PHP 8.2+ · Laravel 12 · MySQL 8+
- Blade + Livewire 3 + Alpine.js + Tailwind CSS 4 (RTL / فارسی)
- RBAC بر پایه `spatie/laravel-permission` با مدل‌ها و جداول اختصاصی `Core`

## نصب سریع (XAMPP)

راهنمای کامل: [`docs/installation-xampp.md`](docs/installation-xampp.md)

```bash
git clone https://github.com/faranetfasa/nursing.git
cd nursing
composer install
npm install && npm run build
```

سپس در مرورگر `http://localhost/nursing/public/install.php` را باز کنید و مراحل نصاب گرافیکی را طی کنید
(خوش‌آمدید → بررسی سیستم → پایگاه داده → سازمان → مدیر سیستم → تنظیمات → نصب → داده نمونه → پایان).

نصاب فایل `.env` را می‌سازد، `APP_KEY` تولید می‌کند، مهاجرت‌ها و داده‌های پایه را اجرا می‌کند، `storage:link` می‌سازد و در پایان `storage/installed.lock` را ایجاد می‌کند تا نصب دوباره قفل شود. تا زمانی که این فایل وجود نداشته باشد، میان‌افزار `EnsureApplicationIsInstalled` همه درخواست‌ها را به نصاب هدایت می‌کند.

نصب بدون رابط گرافیکی:

```bash
NURSING_ADMIN_PASSWORD='رمز-دلخواه' php artisan nursing:install --fresh --demo \
  --org-name="موسسه نمونه" --admin-username=admin
```

رمز عبور مدیر از متغیر محیطی `NURSING_ADMIN_PASSWORD` خوانده می‌شود تا در فهرست پردازه‌ها دیده نشود؛ اگر تعیین نشود، یک رمز تصادفی ساخته و یک‌بار در خروجی نمایش داده می‌شود.

## مستندات

| سند | توضیح |
| --- | --- |
| [`docs/installation-xampp.md`](docs/installation-xampp.md) | نصب روی XAMPP و اجرای نصاب |
| [`docs/architecture.md`](docs/architecture.md) | ساختار پروژه، سیستم ماژول و اصول معماری |
| [`docs/database-map.md`](docs/database-map.md) | جداول این فاز + نقشه کامل دیتابیس فازهای بعد |
| [`docs/roadmap.md`](docs/roadmap.md) | نقشه راه ماژول‌ها و بندهای باقی‌مانده Specification |

## توسعه

```bash
php artisan serve            # اجرای برنامه
npm run dev                  # Vite در حالت توسعه
php artisan test             # اجرای تست‌ها
```

## مجوز

[MIT](LICENSE)
