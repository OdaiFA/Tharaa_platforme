# ثراء — Tharaa

منصة تعليم مالي تفاعلية باللغة العربية (RTL) لتحسين الثقافة المالية، مبنيّة على **Laravel 12** مع **MySQL/MariaDB** و **Tailwind CSS v4** و **Alpine.js**.

## المميزات

- **المصادقة والدخول**: تسجيل/تسجيل دخول/خروج، حماية من التكرار عبر `throttle:5,1`، دور `admin` مقابل دور `user`.
- **لوحة المستخدم**:
  - حسابات مالية (نقدي/بنكي/ادخاري/إلكتروني) مع رصيد يُعاد احتسابه تلقائياً من المعاملات.
  - معاملات (دخل/مصروف/تحويل بين الحسابات) ومعاملات متكررة تُنفذ يومياً تلقائياً.
  - ميزانيات (إجمالية + لكل فئة) مع تنبيهات عند تجاوز العتبة.
  - أهداف ادخار مع مساهمات ومراحل إنجاز.
  - دورات تعليمية: تسجيل، دروس، محتوى، اختبارات بعد كل درس مع عدد محاولات ودرجة نجاح، تقدم، شهادات.
  - مقالات التوعية المالية وإشعارات داخل التطبيق.
- **لوحة الأدمن**: إدارة المستخدمين، الدورات، الوحدات، الدروس، الاختبارات والأسئلة، المقالات، الفئات، الفئات العمرية، وإحصائيات.
- **REST API**: مصادقة عبر Sanctum + تغطية كاملة للوحدات (حسابات، معاملات، ميزانيات، أهداف، دورات، اختبارات، إشعارات، مقالات).

## المتطلبات

- PHP ≥ 8.2
- MySQL/MariaDB
- Composer
- Node.js (لبناء Tailwind v4)

## الإعداد

```bash
# 1. تثبيت الاعتماديات
composer install
npm install && npm run build

# 2. إعداد البيئة
cp .env.example .env
php artisan key:generate
```

عدّل `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tharaa
DB_USERNAME=root
DB_PASSWORD=
```

ثم:

```bash
php artisan migrate --seed
php artisan serve
```

افتح `http://127.0.0.1:8000`.

### حساب الأدمن الافتراضي

| البريد | كلمة المرور |
|--------|-------------|
| `admin@tharaa.sa` | `password` |

> أنشأه `DatabaseSeeder` (دور `admin`). لاحظ أن مصادقة دخول الأدمن تعتمد على البريد + كلمة المرور المعرّفة في seeder.

## بنية المشروع

```
app/
├── Http/
│   ├── Controllers/          # Web controllers + Admin/ + Api/
│   ├── Middleware/           # EnsureUserIsAdmin, throttle aliases...
│   └── Requests/             # FormRequests للمصادقة والموارد
├── Models/                   # 21 نموذج Eloquent
├── Observers/                # Enrollment, GoalContribution, LessonProgress, QuizAttempt
├── Repositories/             # طبقة بيانات قابلة للحقن (BaseRepository...)
├── Services/                 # منطق الأعمال (قابل للاختبار)
│   ├── AuthService, TransactionService, BudgetService,
│   ├── GoalService, QuizService, EnrollmentService, NotificationService
├── Events/ + Listeners/      # UserRegistered, TransactionCreated, GoalContributionAdded, LessonCompleted...
├── Notifications/            # WelcomeNotification وغيره (ShouldQueue)
└── Jobs/                     # ProcessRecurringTransactions, SendGoalDeadlineReminders, SendCourseProgressReminders
bootstrap/app.php             # تكوين middleware، وتحميل routes/admin.php داخل مجموعة web
routes/
├── web.php, admin.php, api.php, console.php
```

### تصميم متسق

- **المنطق في الخدمات**: قواعد العمل مكتوبة في `app/Services` (مثلاً احتساب الرصيد، استهلاك الميزانية، التهديف، إصدار الشهادات) — وهي الهدف الرئيسي للاختبارات.
- **المصادر متعددة نقاط الصدق**: `GoalContributionObserver` يعيد احتساب `current_amount` من مجموع المساهمات عند كل إنشاء، ويتكامل مع `GoalContributionAdded` listener.
- **الرصيد لا ينخفض عن صفر**: `TransactionService::recalculateAccount` يثبّت الرصيد عند `max(balance, 0)` (تصميم مقصود).
- **مصيدة `each()`**: استدعاء `each()` على نموذج Eloquent مفرد يُمرَّر إلى Query Builder ويتكرر على كل الصفوف — اجعل المتغير `$budget = Budget::factory()->create(...); foreach ($budget->...)`.

## المخطط والبيانات التجريبية

`php artisan migrate:fresh --seed` يُنتج بيانات نظيفة:

| الجدول | العدد |
|--------|------:|
| users | 21 (أدمن + 20 مستخدم) |
| accounts | 40 |
| transactions | 2720 |
| budgets / budget_categories | 20 / 60 |
| goals / goal_contributions | 40 / 120 |
| courses / modules / lessons | 4 / 8 / 24 |
| quizzes / quiz_questions | 24 / 96 |
| enrollments / lesson_progress | 15 / 90 |
| quiz_attempts | 44 |
| articles | 8 |

## الجدولة (Scheduler)

في `routes/console.php`:

| التوقيت | المهمة |
|---------|--------|
| يومياً 00:01 | `ProcessRecurringTransactions` — تنفيذ المعاملات المتكررة (idempotent) |
| يومياً 00:05 | `SendGoalDeadlineReminders` |
| يومياً 00:10 | `SendCourseProgressReminders` |

شغّل `php artisan schedule:work` (أو cron) على الخادم.

## الاختبارات

الاختبارات تعمل على MySQL عبر `phpunit.xml` (DB `tharaa_test`) لأن المخطط يستخدم enum خاص بـ MySQL.

```bash
# تأكد من وجود قاعدة الاختبارات
mysql -u root -e "CREATE DATABASE IF NOT EXISTS tharaa_test"

php artisan test
```

التغطية:
- **Unit**: `TransactionService` (احتساب الرصيد، الرصيد الصفري، التحويلات، المعاملات المتكررة، الحذف)، `BudgetService` (الاستهلاك، العتبات، فئات الميزانية)، `GoalService` (المساهمات، الاكتمال، المراحل)، `QuizService` (الحد الأقصى للمحاولات، التهديف، الاستنفاد).
- **Feature**: تدفق المصادقة، حماية أدمن (403 لمستخدم عادي)، سياسات الملكية على الحسابات.

> ملاحظة: `POST /login` محدود بـ `throttle:5,1` — اختبارات الدخول المتكررة قد تصطدم بحد 429.

## ملاحظات تطويرية

- **Laravel 12 `then:` closure**: `routes/admin.php` محمّل داخل مجموعة `web` في `bootstrap/app.php`؛ بدونها تفتقد مسارات الأدمن للجلسة/Cookies/CSRF وتفشل بـ "Session store not set" أو 403 عشوائي.
- تحقق من أخطاء التشغيل في `storage/logs/laravel.log`.
