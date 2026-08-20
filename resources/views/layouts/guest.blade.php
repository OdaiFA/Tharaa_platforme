<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'ثراء')) — منصة ثراء للتعليم المالي</title>
    <link rel="preload" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/fonts/thmanyah/thmanyahsans-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data class="min-h-screen bg-[#f8fafc] text-gray-900">

    <div class="flex min-h-screen flex-col">
        <header class="mx-auto flex w-full max-w-5xl items-center justify-between px-4 py-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-gradient text-lg font-extrabold text-white shadow-lg shadow-primary-600/30">ثر</span>
                <span class="text-xl font-extrabold text-navy-800">ثراء</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-bold text-white shadow-md shadow-primary-600/25 hover:bg-primary-700">لوحة التحكم</a>
            @else
                <a href="{{ route('login') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-bold text-white shadow-md shadow-primary-600/25 hover:bg-primary-700">تسجيل الدخول</a>
            @endauth
        </header>

        <main class="mx-auto w-full max-w-5xl flex-1 px-4 pb-12">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>
</body>
</html>
