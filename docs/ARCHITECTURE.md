# Architecture

## هدف

سامانه به‌صورت **Modular Monolith** ساخته می‌شود تا همه جریان‌های بیمار، خدمت، قرارداد، اعزام، مالی، کمیسیون و تسویه در یک مدل دامنه یکپارچه و قابل نگهداری باقی بمانند.

## لایه‌ها

1. **Presentation**: Blade، Livewire، Tailwind، Alpine.js، RTL و تقویم شمسی.
2. **HTTP/API**: Controllerهای وب و REST API با Form Request فارسی.
3. **Application Service Layer**: هماهنگی Use Caseها مانند `CreateServiceRequestService` و `SettleWalletService`.
4. **Domain Layer**: Modelها، Enumها، Policyها، Eventها و Ruleهای دامنه.
5. **Infrastructure**: Queue، Scheduler، Storage، SMS، Payment Gateway، Email، Backup.

## جریان بیمار

`Patient -> ServiceRequest -> Service -> Contract -> Assignment -> Shift -> Dispatch -> ServiceReport -> Invoice -> Payment -> Commission -> Wallet -> Settlement`

این جریان با کلیدهای خارجی، شناسه‌های یکتا، رویدادهای دامنه و Audit Log قابل ردیابی خواهد بود.

## جریان پرسنل

`Employee -> PersonnelFile -> EmployeeContract -> Shift -> Payroll -> Payslip -> Warning -> Documents -> Assets -> Settlement`

## الگوهای معماری

- Service Layer برای عملیات چندمرحله‌ای.
- Event Driven Architecture برای رخدادهای بین ماژولی.
- Policy و Permission Gate برای کنترل دسترسی.
- Repository فقط در موارد گزارش‌گیری پیچیده یا Integration اضافه می‌شود.
- Gateway Interface برای SMS، Payment و External API.
- Soft Delete برای داده‌های حساس و Audit Log برای عملیات حساس.

## رویدادهای نمونه

- `PatientRegistered`
- `ServiceRequestCreated`
- `ProviderAssigned`
- `DispatchCompleted`
- `InvoiceIssued`
- `PaymentApproved`
- `CommissionCalculated`
- `SettlementPaid`
- `MedicalRecordViewed`

## امنیت

- داده پزشکی در جداول اختصاصی امن نگهداری می‌شود، نه صرفاً JSON عمومی Dynamic Module.
- مشاهده، چاپ، خروجی گرفتن و امضای داده حساس در Audit Log ثبت می‌شود.
- خطاهای Production دارای Error ID هستند و Stack Trace به کاربر نمایش داده نمی‌شود.
