<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة الإدارة') — ثراء</title>
    <link rel="preload" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/fonts/thmanyah/thmanyahsans-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data class="min-h-screen bg-[#f8fafc] text-gray-900">

    <nav class="sticky top-0 z-40 border-b border-white/10 bg-hero-gradient text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3">
            <div class="flex items-center gap-2">
                <button class="rounded-lg p-2 text-gray-300 hover:bg-white/10 lg:hidden" @click="$store.nav.toggle()" aria-label="القائمة">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-gradient text-sm font-extrabold">ثر</span>
                    <span class="font-bold">لوحة الإدارة</span>
                </a>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('dashboard') }}" class="hidden text-gray-300 hover:text-white sm:block">العودة للموقع</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-1 rounded-lg bg-white/10 px-3 py-1.5 font-medium text-red-300 hover:bg-white/20 hover:text-red-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden sm:inline">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="mx-auto flex max-w-7xl gap-6 px-4 py-6">
        <aside x-cloak class="fixed inset-y-0 right-0 z-50 w-60 -translate-x-full transform bg-white p-4 shadow-xl transition-transform lg:static lg:z-auto lg:block lg:w-52 lg:translate-x-0 lg:shadow-none"
            :class="$store.nav.open ? 'translate-x-0' : '-translate-x-full'">
            <div class="mb-4 flex items-center justify-between lg:hidden">
                <span class="text-lg font-extrabold text-navy-800">لوحة الإدارة</span>
                <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" @click="$store.nav.open = false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">نظرة عامة</a>
                <a href="{{ route('admin.statistics') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.statistics') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">إحصائيات</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">المستخدمون</a>
                <a href="{{ route('admin.courses.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.courses.*') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">الدورات</a>
                <a href="{{ route('admin.articles.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.articles.*') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">المقالات</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.categories.index') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">تصنيفات المعاملات</a>
                <a href="{{ route('admin.article-categories.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.article-categories.*') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">تصنيفات المقالات</a>
                <a href="{{ route('admin.age-groups.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.age-groups.*') ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">الفئات العمرية</a>
            </nav>
        </aside>

        <main class="min-w-0 flex-1">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('nav', { open: false, toggle() { this.open = !this.open; } });
        });
    </script>
    @stack('scripts')
    @livewireScriptConfig
</body>
</html>
