@extends('layouts.admin')

@section('title', 'نظرة عامة')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">نظرة عامة</h1>
        <p class="mt-1 text-sm text-gray-500">ملخص سريع لحالة المنصة</p>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">المستخدمون</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900">{{ $stats['users'] }}</p>
            <p class="mt-1 text-xs text-green-600">+{{ $stats['new_users_this_month'] }} هذا الشهر</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">الدورات</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900">{{ $stats['courses'] }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['published_courses'] }} منشورة</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">التسجيلات</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900">{{ $stats['enrollments'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">المعاملات</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900">{{ $stats['transactions'] }}</p>
            <p class="mt-1 text-xs text-gray-500">
                بحجم
                @forelse ($stats['transaction_volume_by_currency'] as $currency => $amount)
                    {{ number_format($amount, 2) }} {{ $currency }}@if (! $loop->last),@endif
                @empty
                    0.00
                @endforelse
            </p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">أحدث المستخدمين</h2>
            <div class="space-y-3">
                @forelse ($recentUsers as $user)
                    <a href="{{ route('admin.users.show', $user) }}" class="flex items-center justify-between border-b border-gray-50 pb-3 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700">{{ mb_substr($user->name, 0, 1) }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400">لا يوجد مستخدمون بعد</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">أحدث التسجيلات</h2>
            <div class="space-y-3">
                @forelse ($recentEnrollments as $enrollment)
                    <div class="flex items-center justify-between border-b border-gray-50 pb-3 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $enrollment->course->title }}</p>
                            <p class="text-xs text-gray-400">{{ $enrollment->user->name }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $enrollment->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">لا توجد تسجيلات بعد</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-bold text-gray-900">نمو المستخدمين (آخر 12 شهراً)</h2>
        @if ($userGrowth->isNotEmpty())
            <canvas id="userGrowthChart" height="90"></canvas>
        @else
            <p class="text-sm text-gray-400">لا توجد بيانات بعد</p>
        @endif
    </section>
@endsection

@push('scripts')
@if ($userGrowth->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: @json($userGrowth->keys()),
                datasets: [{
                    label: 'مستخدمون جدد',
                    data: @json($userGrowth->values()),
                    borderColor: '#1c80f1',
                    backgroundColor: 'rgba(28,128,241,0.1)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    });
</script>
@endif
@endpush
