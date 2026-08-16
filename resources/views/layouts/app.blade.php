<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'ثراء')) — منصة ثراء للتعليم المالي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data class="min-h-screen bg-[#f8fafc] text-gray-900">

    <nav class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3">
            <div class="flex min-w-0 items-center gap-3">
                <button class="shrink-0 rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden" @click="$store.nav.toggle()" aria-label="القائمة">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('landing') }}" class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-gradient text-lg font-extrabold text-white shadow-md shadow-primary-600/30">ثر</span>
                    <span class="text-xl font-extrabold text-navy-800">ثراء</span>
                </a>
            </div>

            <div class="hidden items-center gap-1 lg:flex">
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">لوحة التحكم</a>
                <a href="{{ route('accounts.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">الحسابات</a>
                <a href="{{ route('transactions.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">المعاملات</a>
                <a href="{{ route('budgets.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">الميزانيات</a>
                <a href="{{ route('goals.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">الأهداف</a>
                <a href="{{ route('courses.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700">الدورات</a>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="الإشعارات">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if (auth()->user()->userNotifications()->unread()->exists())
                        <span class="absolute -top-1 -left-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                            {{ auth()->user()->userNotifications()->unread()->count() }}
                        </span>
                    @endif
                </a>

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-gray-100" @click="open = !open">
                        <span class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-brand-gradient text-sm font-bold text-white">
                            {{ auth()->user()->avatar_url ? '' : mb_substr(auth()->user()->name, 0, 1) }}
                        </span>
                        <span class="hidden text-sm font-medium text-gray-700 sm:block">{{ auth()->user()->name }}</span>
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute left-0 z-50 mt-2 w-48 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">الملف الشخصي</a>
                        <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">الإعدادات</a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">لوحة الإدارة</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-right text-sm text-red-600 hover:bg-red-50">تسجيل الخروج</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="mx-auto flex max-w-7xl gap-6 px-4 py-6">
        @include('layouts.partials.sidebar')

        <main class="min-w-0 flex-1">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>
    </div>

    @include('layouts.partials.footer')

    @include('layouts.partials.bottom-nav')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('nav', { open: false, toggle() { this.open = !this.open; } });
        });
    </script>
    @stack('scripts')
</body>
</html>
