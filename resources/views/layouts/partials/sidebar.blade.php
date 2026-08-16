<aside x-cloak class="fixed inset-y-0 right-0 z-50 flex w-64 -translate-x-full transform flex-col bg-white p-4 shadow-xl transition-transform lg:static lg:z-auto lg:block lg:w-60 lg:translate-x-0 lg:shadow-none"
    :class="$store.nav.open ? 'translate-x-0' : '-translate-x-full'">

    <div class="mb-4 flex items-center justify-between lg:hidden">
        <span class="text-lg font-extrabold text-navy-800">القائمة</span>
        <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" @click="$store.nav.open = false">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="mb-4 hidden rounded-2xl bg-brand-gradient p-4 text-white lg:block">
        <p class="text-sm font-bold">أهلاً بك في ثراء</p>
        <p class="mt-0.5 text-xs text-primary-100">ادخر، وتعلم، ونمّ ثروتك</p>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            لوحة التحكم
        </a>

        <p class="px-3 pt-4 pb-1 text-xs font-bold text-gold-600">الإدارة المالية</p>
        <a href="{{ route('accounts.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('accounts.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            الحسابات
        </a>
        <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('transactions.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            المعاملات
        </a>
        <a href="{{ route('budgets.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('budgets.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            الميزانيات
        </a>
        <a href="{{ route('goals.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('goals.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            الأهداف
        </a>

        <p class="px-3 pt-4 pb-1 text-xs font-bold text-gold-600">التعلم</p>
        <a href="{{ route('courses.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('courses.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            الدورات التدريبية
        </a>
        <a href="{{ route('articles.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('articles.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            المقالات
        </a>

        <p class="px-3 pt-4 pb-1 text-xs font-bold text-gold-600">عام</p>
        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            الإعدادات
        </a>
    </nav>
</aside>
