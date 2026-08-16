<nav class="fixed bottom-0 right-0 left-0 z-40 border-t border-gray-200 bg-white pb-[env(safe-area-inset-bottom)] lg:hidden">
    <div class="grid grid-cols-5 px-2 py-1.5">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 rounded-lg py-1.5 text-[11px] {{ request()->routeIs('dashboard') ? 'font-bold text-primary-600' : 'text-gray-500' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            الرئيسية
        </a>
        <a href="{{ route('accounts.index') }}" class="flex flex-col items-center gap-0.5 rounded-lg py-1.5 text-[11px] {{ request()->routeIs('accounts.*') ? 'font-bold text-primary-600' : 'text-gray-500' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            الحسابات
        </a>
        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-0.5 rounded-lg py-1.5 text-[11px] {{ request()->routeIs('transactions.*') ? 'font-bold text-primary-600' : 'text-gray-500' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-10H4"/></svg>
            المعاملات
        </a>
        <a href="{{ route('courses.index') }}" class="flex flex-col items-center gap-0.5 rounded-lg py-1.5 text-[11px] {{ request()->routeIs('courses.*') ? 'font-bold text-primary-600' : 'text-gray-500' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            الدورات
        </a>
        <a href="{{ route('goals.index') }}" class="flex flex-col items-center gap-0.5 rounded-lg py-1.5 text-[11px] {{ request()->routeIs('goals.*') ? 'font-bold text-primary-600' : 'text-gray-500' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            الأهداف
        </a>
    </div>
</nav>
