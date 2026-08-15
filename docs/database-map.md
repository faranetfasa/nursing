# نقشه پایگاه داده

قواعد عمومی: کلید اصلی `bigint unsigned`، همه روابط با Foreign Key، ایندکس روی ستون‌های جست‌وجو،
`utf8mb4_unicode_ci` و Soft Delete (`deleted_at`) روی جداول حساس.

## جداول ساخته‌شده در فاز Foundation

| جدول | Soft Delete | توضیح | کلیدهای خارجی |
| --- | --- | --- | --- |
| `organizations` | ✔ | سازمان (نام، اطلاعات حقوقی، آدرس، لوگو) | `manager_id → users` |
| `branches` | ✔ | شعبه‌ها با موقعیت جغرافیایی و شعبه اصلی | `organization_id`, `manager_id` |
| `departments` | ✔ | دپارتمان‌های درختی (`parent_id`) | `organization_id`, `branch_id`, `parent_id`, `manager_id` |
| `positions` | ✔ | سمت‌های سازمانی با سطح | `organization_id`, `department_id` |
| `users` | ✔ | کاربران با `username`, `mobile`, وضعیت و اطلاعات آخرین ورود | `organization_id`, `branch_id`, `department_id`, `position_id` |
| `roles` | — | نقش‌ها با `display_name`, `level`, `is_system` | — |
| `permissions` | — | دسترسی‌ها با `group` و `action` | — |
| `user_roles` | — | چند نقش برای هر کاربر (Pivot) | `user_id`, `role_id` |
| `role_permissions` | — | دسترسی‌های هر نقش (Pivot) | `role_id`, `permission_id` |
| `user_permissions` | — | دسترسی مستقیم به کاربر (Pivot) | `user_id`, `permission_id` |
| `settings` | — | تنظیمات کلید/مقدار با گروه، نوع و پرچم رمزنگاری | `organization_id` |
| `audit_logs` | — | رخدادهای حساس با مقادیر قبل/بعد، IP، URL و `error_id` | `user_id`, `organization_id` |
| `notifications` | — | اعلان‌ها (UUID) با کانال، اولویت و `read_at` | polymorphic `notifiable` |
| `login_histories` | — | تاریخچه ورود موفق/ناموفق با دستگاه و مرورگر | `user_id` |
| `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs` | — | جداول زیرساختی Laravel | — |

### ارتباط‌های اصلی

```
organizations 1─n branches 1─n departments 1─n positions
organizations 1─n users        users n─n roles n─n permissions
users 1─n login_histories      users 1─n audit_logs      users 1─n notifications
organizations 1─n settings
```

## جداول فازهای بعدی (نقشه کامل)

این جداول در این فاز ساخته نشده‌اند و همراه ماژول مربوطه اضافه می‌شوند.

| ماژول | جداول برنامه‌ریزی‌شده |
| --- | --- |
| HR | `employees`, `employee_documents`, `employee_contracts`, `attendances`, `leaves`, `leave_types`, `shifts`, `shift_assignments`, `trainings`, `certificates`, `performance_reviews` |
| Patients | `patients`, `patient_addresses`, `patient_contacts`, `patient_documents`, `medical_histories`, `allergies`, `vital_signs`, `patient_files` |
| CRM | `leads`, `lead_sources`, `opportunities`, `activities`, `follow_ups`, `campaigns`, `customer_notes` |
| Services | `service_categories`, `services`, `service_prices`, `service_packages`, `service_requests`, `service_request_items` |
| Nursing | `nursing_orders`, `care_plans`, `care_plan_items`, `visits`, `visit_reports`, `nurse_assignments`, `nursing_notes`, `wound_care_records`, `medication_administrations` |
| Medical | `doctors`, `doctor_schedules`, `prescriptions`, `prescription_items`, `lab_orders`, `lab_results`, `imaging_orders`, `diagnoses`, `icd_codes` |
| Contracts | `contracts`, `contract_parties`, `contract_items`, `contract_renewals`, `contract_documents`, `sla_terms` |
| Finance | `accounts`, `journal_entries`, `journal_entry_lines`, `invoices`, `invoice_items`, `payments`, `receipts`, `expenses`, `cost_centers`, `tariffs`, `discounts`, `bank_accounts`, `transactions` |
| Payroll | `payroll_periods`, `payslips`, `payslip_items`, `salary_structures`, `allowances`, `deductions`, `loans`, `loan_installments`, `overtime_records` |
| Insurance | `insurers`, `insurance_plans`, `patient_insurances`, `insurance_claims`, `claim_items`, `claim_batches`, `coverage_rules` |
| Pharmacy | `medicines`, `medicine_categories`, `drug_interactions`, `pharmacy_stocks`, `dispenses`, `dispense_items`, `purchase_orders` |
| Inventory | `warehouses`, `items`, `item_categories`, `units`, `stock_levels`, `stock_movements`, `stock_takes`, `requisitions`, `suppliers`, `purchase_invoices` |
| Assets | `assets`, `asset_categories`, `asset_assignments`, `maintenances`, `maintenance_schedules`, `depreciations`, `asset_transfers` |
| Hospitals | `hospitals`, `hospital_contracts`, `wards`, `beds`, `bed_assignments`, `admissions`, `discharges`, `referrals` |
| Ambulance | `vehicles`, `vehicle_documents`, `vehicle_maintenances`, `drivers`, `ambulance_missions`, `mission_crew`, `fuel_records` |
| Dispatch | `dispatch_requests`, `dispatch_assignments`, `dispatch_routes`, `staff_availabilities`, `geo_zones`, `tracking_logs` |
| Automation | `workflows`, `workflow_steps`, `workflow_instances`, `workflow_tasks`, `approval_chains`, `approvals`, `triggers`, `scheduled_jobs` |
| Communication | `messages`, `message_threads`, `sms_logs`, `email_logs`, `notification_templates`, `announcements`, `internal_letters` |
| Support | `tickets`, `ticket_categories`, `ticket_messages`, `ticket_attachments`, `ticket_slas`, `knowledge_articles` |
| Satisfaction | `surveys`, `survey_questions`, `survey_responses`, `survey_answers`, `complaints`, `complaint_actions`, `nps_scores` |
| Consultation | `consultation_requests`, `consultation_sessions`, `consultant_profiles`, `consultation_notes`, `consultation_fees` |
| Forms | `form_definitions`, `form_fields`, `form_sections`, `form_submissions`, `form_submission_values`, `form_permissions` |
| DynamicModules | `dynamic_modules`, `dynamic_entities`, `dynamic_fields`, `dynamic_records`, `dynamic_record_values`, `dynamic_menus` |

جداول مشترک آینده: `attachments`, `comments`, `tags`, `taggables`, `imports`, `exports`,
`report_definitions`, `dashboard_widgets`, `api_tokens`, `webhooks`.
