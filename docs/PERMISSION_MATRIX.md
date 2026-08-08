# Permission Matrix

## Actions

`view`, `create`, `edit`, `delete`, `approve`, `reject`, `print`, `export`, `assign`, `pay`, `manage`, `sign`

## Roles اولیه Seed

Super Admin، CEO، HR Manager، Finance Manager، Nursing Manager، IT Manager، Warehouse Manager، Hospital Manager، Reception، Doctor، Psychologist، Nurse، Caregiver، Patient.

## Matrix خلاصه

| Module | Super Admin | CEO | HR | Finance | Nursing | IT | Reception | Provider | Patient |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Core Settings | manage | view | - | - | - | manage | - | - | - |
| Users/RBAC | manage | view | create/edit | - | - | manage | - | - | - |
| HR | manage | view/approve | manage | payroll-view | view | - | - | self-view | - |
| Patients | manage | view | - | invoice-view | care-view | - | create/edit | assigned-view | self-view |
| Medical | manage | restricted-view | - | - | restricted-view | - | - | assigned-view | self-view |
| Services | manage | view | provider-view | financial-view | manage | - | create/edit | assigned-view | request |
| Contracts | manage | approve/sign | employee-contract | financial-view | service-view | - | create | assigned-view | self-view/sign |
| Finance | manage | view/approve | payroll-view | manage | commission-view | - | payment-create | wallet-view | self-payment |
| Dispatch | manage | view | - | cost-view | manage | - | create | assigned-update | self-view |
| Tickets | manage | view | create | create | create | manage | create | create | create |

## قواعد مهم

- دسترسی مستقیم User، Department و Position می‌تواند Role را محدودتر یا گسترده‌تر کند.
- هر کاربر می‌تواند چند Role و چند Position داشته باشد.
- عملیات حساس پزشکی علاوه بر Permission نیازمند Scope ارتباط با بیمار است.
