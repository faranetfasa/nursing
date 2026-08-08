# Installer Architecture

## ساختار

Installer مستقل در `installer/` قرار دارد و در Runtime عادی سیستم فعال نیست. مسیر `/install` باید به `installer/index.php` یا Route معادل Apache هدایت شود.

## Wizard Steps

1. Welcome
2. Requirements
3. Database
4. Organization
5. Administrator
6. Configuration
7. Installation
8. Complete

## Services

- `RequirementChecker`: بررسی PHP، Extensionها، Permissionها، Disk، Memory، Upload و Timeout.
- `DatabaseTester`: تست اتصال PDO MySQL و امکان Create Database.
- `EnvironmentWriter`: تولید `.env` بدون Hard Code.
- `MigrationRunner`: اجرای Migration و Seed با Progress قابل گزارش.
- `InstallLock`: ایجاد و بررسی `storage/app/installed.lock` یا `installer/storage/installed.lock`.

## Security

پس از نصب موفق، Installer قفل می‌شود. اجرای مجدد فقط با حذف امن Lock و تایید Super Admin در حالت Repair/Update ممکن خواهد بود.

## Future Update Flow

`Check Version -> Backup -> Download/Apply Update -> Run Migration -> Health Check -> Rollback on Failure`

## Demo Mode

Installer می‌تواند Seed دمو شامل بیمار، کارمند، قرارداد، فاکتور و داشبورد نمونه را اجرا کند.
