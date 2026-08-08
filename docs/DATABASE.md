# Database Design

## اصول

- MySQL 8+ و InnoDB.
- کلید اصلی `id` از نوع unsigned big integer.
- شناسه کسب‌وکاری قابل تنظیم مانند `PAT-000001` در ستون `code`.
- همه جدول‌های حساس شامل `created_by`, `updated_by`, `deleted_at` و Audit Log هستند.
- تغییر Migrationهای قبلی ممنوع است؛ فقط Migration جدید اضافه می‌شود.

## Core Tables

- `users`, `roles`, `permissions`, `user_roles`, `role_permissions`
- `organizations`, `branches`, `departments`, `positions`, `user_positions`
- `settings`, `audit_logs`, `notifications`, `files`

## Patient & Care Flow

- `patients`, `patient_documents`, `patient_conditions`, `patient_medications`, `patient_contacts`
- `patient_triages`, `patient_vitals`, `patient_care_records`
- `services`, `service_requests`, `service_assignments`, `service_reports`, `shifts`, `appointments`
- `contracts`, `contract_parties`, `contract_items`, `contract_documents`
- `invoices`, `invoice_items`, `payments`, `commissions`, `wallets`, `wallet_transactions`, `settlements`

## HR Flow

- `employees`, `employee_documents`, `employee_contracts`, `employee_warnings`
- `employee_attendance`, `employee_leaves`, `employee_payrolls`, `payslips`
- `assets`, `asset_assignments`, `asset_transfers`

## Dynamic Builder

- `forms`, `form_fields`, `form_submissions`
- `modules`, `module_fields`, `module_records`, `module_relations`, `module_views`, `module_permissions`, `module_workflows`

## Index Strategy

- Unique index روی `code` در موجودیت‌های اصلی.
- Index روی `status`, `branch_id`, `organization_id`, `created_at`.
- Foreign key برای مسیرهای اصلی مالی و درمانی.
- Composite index برای گزارش‌های پرتکرار مانند `(patient_id, status)` و `(provider_id, starts_at)`.
