@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <div class="animate-fade-in">
        <h1 class="text-2xl font-extrabold text-gray-900">مرحباً، {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ now()->translatedFormat('l، d F Y') }} — إليك ملخصك المالي
        </p>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-navy-800">مرحباً، {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ now()->translatedFormat('l، d F Y') }} — إليك ملخصك المالي
                </p>
            </div>
            <span class="xp-badge">
                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zm7-10a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/></svg>
                المستوى: {{ match (auth()->user()->financial_level) { 'beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم', default => auth()->user()->financial_level } }}
            </span>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="bg-financial-card relative overflow-hidden p-6 text-white md:col-span-1">
                <div class="pointer-events-none absolute -top-10 -left-10 h-32 w-32 rounded-full bg-gold-500/20 blur-2xl"></div>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-300">إجمالي الأرصدة</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10">
                        <svg class="h-5 w-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </span>
                </div>
                @forelse ($balanceByCurrency as $currency => $amount)
                    <p class="font-num mt-3 text-3xl font-bold">{{ number_format($amount, 2) }}</p>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $currency }}</p>
                @empty
                    <p class="font-num mt-3 text-3xl font-bold">0.00</p>
                    <p class="mt-0.5 text-xs text-slate-400">{{ auth()->user()->currency }}</p>
                @endforelse
            </div>
            <div class="rounded-2xl border border-green-100 bg-green-50 p-6">
                <p class="text-sm font-medium text-green-700">مداخيل هذا الشهر</p>
                @forelse ($incomeByCurrency as $currency => $amount)
                    <p class="font-num mt-3 text-2xl font-extrabold text-green-700">{{ number_format($amount, 2) }}</p>
                    <p class="mt-0.5 text-xs text-green-600/70">{{ $currency }}</p>
                @empty
                    <p class="font-num mt-3 text-2xl font-extrabold text-green-700">0.00</p>
                    <p class="mt-0.5 text-xs text-green-600/70">{{ auth()->user()->currency }}</p>
                @endforelse
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50 p-6">
                <p class="text-sm font-medium text-red-700">مصاريف هذا الشهر</p>
                @forelse ($expenseByCurrency as $currency => $amount)
                    <p class="font-num mt-3 text-2xl font-extrabold text-red-700">{{ number_format($amount, 2) }}</p>
                    <p class="mt-0.5 text-xs text-red-600/70">{{ $currency }}</p>
                @empty
                    <p class="font-num mt-3 text-2xl font-extrabold text-red-700">0.00</p>
                    <p class="mt-0.5 text-xs text-red-600/70">{{ auth()->user()->currency }}</p>
                @endforelse
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gold-100 text-xl">🎯</span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">أهداف مكتملة</p>
                    <p class="font-num text-lg font-bold text-navy-800">{{ $completedGoals }}</p>
                </div>
            </div>
            <div class="card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-100 text-xl">📚</span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">الدورات المسجلة</p>
                    <p class="font-num text-lg font-bold text-navy-800">{{ $enrollments->count() }}</p>
                </div>
            </div>
            <div class="card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-100 text-xl">📈</span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">التقدم التعليمي</p>
                        <p class="font-num text-lg font-bold text-navy-800">{{ $learningProgress }}%</p>
                    </div>
                    <div class="progress-track mt-2">
                        <div class="h-full rounded-full bg-gold-gradient transition-all" style="width: {{ $learningProgress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">أحدث المعاملات</h2>
                    <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-primary-600 hover:underline">عرض الكل</a>
                </div>
                @forelse ($recentTransactions as $transaction)
                    <div class="flex items-center justify-between border-b border-gray-50 py-3 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $transaction->type === 'income' ? 'bg-green-50 text-green-600' : ($transaction->type === 'expense' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600') }}">
                                @if ($transaction->type === 'income')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                @endif
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $transaction->description ?: ($transaction->category->name ?? '—') }}</p>
                                <p class="text-xs text-gray-400">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold {{ $transaction->type === 'income' ? 'text-green-600' : ($transaction->type === 'expense' ? 'text-red-600' : 'text-blue-600') }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-400">لا توجد معاملات بعد — <a href="{{ route('transactions.create') }}" class="text-primary-600 hover:underline">أضف معاملة</a></p>
                @endforelse
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">دوراتك</h2>
                    <a href="{{ route('courses.index') }}" class="text-sm font-medium text-primary-600 hover:underline">استكشف</a>
                </div>
                @forelse ($enrollments as $enrollment)
                    <a href="{{ route('courses.learn', $enrollment->course) }}" class="block border-b border-gray-50 py-3 last:border-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800">{{ $enrollment->course->title }}</p>
                            <span class="text-xs font-bold text-primary-600">{{ $enrollment->progress_percentage }}%</span>
                        </div>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-primary-500 transition-all" style="width: {{ $enrollment->progress_percentage }}%"></div>
                        </div>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-gray-400">
                        لم تسجل في أي دورة بعد —
                        <a href="{{ route('courses.recommended') }}" class="text-primary-600 hover:underline">شاهد التوصيات</a>
                    </p>
                @endforelse
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">أهداف الادخار</h2>
                    <a href="{{ route('goals.create') }}" class="text-sm font-medium text-primary-600 hover:underline">هدف جديد</a>
                </div>
                @forelse ($goals as $goal)
                    <div class="border-b border-gray-50 py-3 last:border-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800">{{ $goal->name }}</p>
                            <span class="text-xs font-bold text-gray-500">{{ $goal->progress_percentage }}%</span>
                        </div>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-gold-500 transition-all" style="width: {{ min($goal->progress_percentage, 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-400"><a href="{{ route('goals.create') }}" class="text-primary-600 hover:underline">أنشئ هدفاً للادخار</a></p>
                @endforelse
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">الميزانيات النشطة</h2>
                    <a href="{{ route('budgets.create') }}" class="text-sm font-medium text-primary-600 hover:underline">ميزانية جديدة</a>
                </div>
                @forelse ($activeBudgets as $item)
                    <div class="border-b border-gray-50 py-3 last:border-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800">{{ $item['budget']->name }}</p>
                            <span class="text-xs font-bold {{ $item['consumption']['percentage'] >= 100 ? 'text-red-600' : ($item['consumption']['percentage'] >= 80 ? 'text-amber-600' : 'text-gray-500') }}">
                                {{ $item['consumption']['percentage'] }}%
                            </span>
                        </div>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full {{ $item['consumption']['percentage'] >= 100 ? 'bg-red-500' : 'bg-primary-500' }} transition-all" style="width: {{ $item['consumption']['percentage'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-400"><a href="{{ route('budgets.create') }}" class="text-primary-600 hover:underline">أنشئ ميزانية شهرية</a></p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
