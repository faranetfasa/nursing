# Module Map

## Core

Authentication، Users، Roles، Permissions، Organization، Settings، Notifications، Audit، Files، Workflow، Search، Reports و API در Core قرار می‌گیرند.

## Business Modules

| Module | مسئولیت | وابستگی مستقیم |
| --- | --- | --- |
| HR | پرونده پرسنلی، مدارک، قرارداد پرسنل، اخطار، حضور | Core, Organization |
| Patients | پرونده بیمار، تماس، بیمه، سوابق | Core |
| CRM | Lead، تماس، پیگیری، تبدیل به Patient | Core, Patients |
| Services | تعریف خدمت، درخواست، تخصیص، گزارش | Patients, HR |
| Nursing | فرم‌های پرستاری، کاردکس، مراقبت | Patients, Services |
| Medical | پرونده پزشکی، بیماری، تشخیص، ویزیت | Patients, Core Security |
| Contracts | قراردادها و اسناد | Patients, HR, Services |
| Finance | فاکتور، پرداخت، درآمد، هزینه | Contracts, Services |
| Payroll | حقوق، فیش، کسورات | HR, Finance |
| Wallet | کیف پول، کمیسیون، تسویه | Finance, HR |
| Inventory | انبار، کالا، گردش موجودی | Finance |
| Assets | تجهیزات و تحویل | HR, Inventory |
| Hospitals | بیمارستان، پذیرش، مطالبات | Patients, Contracts |
| Ambulance | آمبولانس، راننده، پرکیس | Dispatch, Finance |
| Dispatch | اعزام، موقعیت، وضعیت‌ها | Services, Ambulance |
| Forms | Form Builder و فرم عمومی | Core |
| DynamicModules | Module Builder Metadata Driven | Core, Workflow |

## قانون وابستگی

ماژول‌ها فقط از Core و Contractهای عمومی ماژول دیگر استفاده می‌کنند. فراخوانی مستقیم منطق داخلی ماژول دیگر ممنوع است.
