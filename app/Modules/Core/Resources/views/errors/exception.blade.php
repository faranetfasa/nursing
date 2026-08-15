<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>خطای سیستمی</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4">
<div class="w-full max-w-lg card text-center">
    <h1 class="text-lg font-semibold text-slate-800">خطای غیرمنتظره</h1>
    <p class="mt-3 text-sm text-slate-500">
        متأسفانه در پردازش درخواست خطایی رخ داد. برای پیگیری، کد خطای زیر را به پشتیبانی اعلام کنید.
    </p>
    <p class="mt-4 rounded-lg bg-slate-100 px-3 py-2 font-mono text-sm text-slate-700" dir="ltr">{{ $errorId }}</p>
    <a href="{{ url('/') }}" class="btn-primary mt-5">بازگشت به صفحه اصلی</a>
</div>
</body>
</html>
