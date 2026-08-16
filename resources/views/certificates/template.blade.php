<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>شهادة إتمام</title>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            font-family: 'Tajawal', 'Segoe UI', Tahoma, sans-serif;
            background: #ffffff;
        }
        .certificate {
            margin: 40px;
            border: 6px double #d4af37;
            padding: 48px 40px;
            text-align: center;
            background: linear-gradient(135deg, #fdfbf3, #ffffff);
        }
        .brand { color: #1c80f1; font-size: 22px; font-weight: 800; letter-spacing: 1px; }
        .title { font-size: 34px; font-weight: 800; color: #142d56; margin-top: 24px; }
        .subtitle { font-size: 16px; color: #6b7280; margin-top: 8px; }
        .divider { width: 120px; height: 2px; background: #d4af37; margin: 24px auto; }
        .name { font-size: 30px; font-weight: 800; color: #1c80f1; margin: 8px 0; }
        .body-text { font-size: 16px; color: #374151; line-height: 1.9; margin-top: 16px; }
        .code { margin-top: 28px; font-size: 13px; color: #9ca3af; letter-spacing: 1px; }
        .footer { margin-top: 28px; font-size: 13px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="brand">منصة ثراء للثقافة المالية</div>
        <div class="title">شهادة إتمام دورة</div>
        <div class="subtitle">تُمنح هذه الشهادة إلى</div>
        <div class="name">{{ $user_name }}</div>
        <div class="divider"></div>
        <p class="body-text">
            لاستكماله بنجاح دورة
            <strong>«{{ $course_title }}»</strong>
            بعدد ساعات {{ $course_duration_hours }} ساعة،
            وقد أظهر التزاماً وفهماً جيداً لمفاهيم الثقافة المالية.
        </p>
        <p class="body-text">تاريخ الإتمام: {{ $completion_date }}</p>
        <div class="code">رمز الشهادة: {{ $certificate_code }}</div>
        <div class="footer">© {{ date('Y') }} منصة ثراء — شهادة إلكترونية</div>
    </div>
</body>
</html>
