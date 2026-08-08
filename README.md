# سامانه جامع مدیریت خدمات سلامت و پرستاری

این مخزن برای ساخت یک **Modular Monolith Enterprise** مبتنی بر Laravel، Livewire، Tailwind CSS، MySQL و Installer مستقل XAMPP آماده شده است.

> وضعیت فعلی: فاز صفر شامل معماری، نقشه ماژول‌ها، ERD سطح کلان، ماتریس دسترسی، ساختار پوشه‌ها و اسکلت Installer مستقل پیاده‌سازی شده است. فازهای بعدی باید به‌ترتیب و پس از تست کامل اجرا شوند.

## مسیرهای کلیدی

- `docs/ARCHITECTURE.md`: تصمیم‌های معماری، جریان‌های دامنه، Service Layer و Event Driven Architecture.
- `docs/DATABASE.md`: گروه‌بندی جداول، قواعد Migration، شناسه‌های یکتا و امنیت داده‌های پزشکی.
- `docs/ERD.md`: ERD متنی Mermaid برای هسته و جریان Patient/Employee.
- `docs/MODULE_MAP.md`: نقشه ماژول‌ها، مرزها و وابستگی‌ها.
- `docs/PERMISSION_MATRIX.md`: مدل RBAC و ماتریس Actionها برای نقش‌های اولیه.
- `docs/INSTALLER_ARCHITECTURE.md`: طراحی Installer گرافیکی، قفل نصب، Repair/Update و Demo Mode.
- `installer/`: اسکلت Installer مستقل قابل توسعه برای مسیر `/install`.
- `modules/`: ساختار ماژولار مطابق فازهای توسعه.

## فازهای توسعه

1. Phase 0: Architecture + Installer foundation
2. Phase 1: Core + Authentication + Users + Roles + Permissions
3. Phase 2: Organization + HR
4. Phase 3: CRM + Patients
5. Phase 4: Services + Requests + Assignments
6. Phase 5: Nursing + Medical + Triage + Kardex
7. Phase 6: Contracts
8. Phase 7: Finance + Billing + Payments
9. Phase 8: Payroll + Wallet + Commission
10. Phase 9: Inventory + Pharmacy + Assets
11. Phase 10: Hospitals + Ambulance + Dispatch
12. Phase 11-21: Builders, Automation, Communication, BI, API, Security, Packaging

## نصب هدف روی XAMPP

پس از کامل شدن فاز Installer:

1. پروژه در `htdocs/healthcare-management` کپی می‌شود.
2. Apache و MySQL روشن می‌شوند.
3. کاربر `/install` را باز می‌کند.
4. Wizard نیازمندی‌ها، دیتابیس، سازمان، مدیر اصلی و تنظیمات اولیه را دریافت می‌کند.
5. `installed.lock` ایجاد و Installer قفل می‌شود.

## اصل توسعه

هیچ Migration قبلی تغییر نمی‌کند؛ برای هر تغییر دیتابیس Migration جدید اضافه می‌شود. هر فاز باید شامل کدنویسی، Migration، Model، Service، Controller، Policy، Permission، Route، UI، Validation و Test باشد.
