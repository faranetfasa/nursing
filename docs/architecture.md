# معماری پروژه

## ساختار پوشه‌ها

```
app/
├── Modules/                 # همه ماژول‌های دامنه
│   ├── Core/                # ماژول پایه (کامل در این فاز)
│   │   ├── Config/core.php
│   │   ├── Console/Commands/InstallCommand.php
│   │   ├── Database/{Migrations,Seeders}
│   │   ├── Http/{Controllers,Middleware,Requests,Livewire}
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Providers/{BaseModuleServiceProvider,ModuleServiceProvider,CoreServiceProvider}.php
│   │   ├── Resources/{views,lang}
│   │   ├── Routes/web.php
│   │   ├── Services/
│   │   └── Support/
│   ├── HR/ … DynamicModules/ # ساختار + ServiceProvider + config + routes (بدون منطق دامنه)
├── Providers/AppServiceProvider.php
config/modules.php           # رجیستری ماژول‌ها
installer/                   # نصاب گرافیکی مستقل (PHP خالص)
public/install.php           # نقطه ورود نصاب
docs/
resources/{css,js}           # Tailwind 4 + Alpine/Livewire
tests/Feature, tests/Unit
```

## سیستم ماژول

- `config/modules.php` رجیستری ماژول‌ها است: هر کلید نام ماژول و مقدار آن `name`, `title`, `enabled`, `phase` دارد.
- `App\Modules\Core\Providers\ModuleServiceProvider` این رجیستری را می‌خواند و برای هر ماژول فعال
  `App\Modules\{Module}\Providers\{Module}ServiceProvider` را ثبت می‌کند. تنها این provider در
  `bootstrap/providers.php` رجیستر شده است.
- هر provider از `BaseModuleServiceProvider` ارث می‌برد و بر اساس قرارداد این موارد را ثبت می‌کند:

| منبع | مسیر | نام‌فضا |
| --- | --- | --- |
| Config | `app/Modules/{M}/Config/{key}.php` | `config('{key}.…')` |
| Migrations | `app/Modules/{M}/Database/Migrations` | — |
| Views | `app/Modules/{M}/Resources/views` | `{key}::view` |
| Translations | `app/Modules/{M}/Resources/lang` | `{key}::messages` |
| Routes | `app/Modules/{M}/Routes/web.php` | با middleware گروه `web` |

کلید ماژول با تبدیل CamelCase به snake_case ساخته می‌شود: `HR` → `hr`، `DynamicModules` → `dynamic_modules`.

افزودن ماژول جدید: پوشه ماژول را بسازید، یک provider با ارث‌بری از `BaseModuleServiceProvider` اضافه
کنید و ماژول را در `config/modules.php` ثبت کنید. هیچ تغییری در هسته لازم نیست.

## ماژول Core

- **احراز هویت** (`Services/AuthService`): ورود با username یا mobile، Remember Me، محدودسازی تلاش‌ها
  (throttle)، رد کاربران غیرفعال، بازیابی رمز، خروج، ثبت `login_histories` و رکورد Audit.
- **RBAC**: `spatie/laravel-permission` با مدل‌های `Core\Models\Role` و `Core\Models\Permission` و جداول
  `roles`, `permissions`, `user_roles`, `role_permissions`, `user_permissions`. هر کاربر می‌تواند چند نقش
  داشته باشد. Permission‌ها granular هستند (`core.users.view`, `core.users.create`, … `.approve`, `.export`).
- **ساختار سازمانی**: `Organization` → `Branch` → `Department` → `Position`.
- **تنظیمات** (`Services/SettingService`): مقادیر در جدول `settings` با گروه/کلید، کش‌شده، با fallback به
  `app/Modules/Core/Config/core.php`. عنوان، لوگو و رنگ سازمانی از همین سرویس خوانده می‌شوند.
- **Audit** (`Services/AuditService`): ثبت عملیات حساس با کاربر، IP، user agent و مقادیر قبل/بعد.
- **اعلان‌ها**: جدول `notifications` و trait `Notifiable` روی `User`.
- **نصب**: `Services/InstallerService` + دستور `php artisan nursing:install`.

## اصول معماری

| اصل | پیاده‌سازی |
| --- | --- |
| Service Layer | منطق کسب‌وکار در `app/Modules/*/Services`؛ کنترلرها فقط هماهنگ‌کننده‌اند |
| بررسی Permission در هر Request | میان‌افزار `permission:` روی route‌ها + Policy‌ها + `PermissionService` |
| Audit عملیات حساس | `AuditService` در ورود/خروج، تغییر تنظیمات و عملیات مدیریتی |
| Error Handling با Error ID | در `bootstrap/app.php` برای هر استثنا یک Error ID تولید و لاگ می‌شود و در view خطا نمایش داده می‌شود |
| ذخیره امن فایل | دیسک `secure` خارج از `public/` و دانلود کنترل‌شده از طریق `core.files.show` |
| عدم Hard Code | همه تنظیمات از جدول `settings`/config/`.env` خوانده می‌شوند |

## Frontend

Blade + Livewire 3 + Alpine.js + Tailwind CSS 4 با جهت RTL و زبان فارسی. Layout داشبورد شامل Sidebar
(فیلترشده بر اساس permission)، Topbar، User Menu، Breadcrumb و حالت Light/Dark است. رنگ سازمانی/لوگو/عنوان
از جدول `settings` تزریق می‌شوند.

## Route های این فاز

`/`, `/login`, `/logout`, `/forgot-password`, `/reset-password/{token}`, `/dashboard`,
`/account/login-history`, `/system/audit-logs`, `/files/{path}`.
