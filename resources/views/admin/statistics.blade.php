@extends('layouts.admin')

@section('title', 'إحصائيات')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">الإحصائيات التحليلية</h1>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">المستخدمون حسب الدور</h2>
            @if ($usersByRole->isNotEmpty())
                <canvas id="usersByRoleChart" height="120"></canvas>
            @else
                <p class="text-sm text-gray-400">لا توجد بيانات</p>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">التسجيلات حسب الحالة</h2>
            @if ($enrollmentsByStatus->isNotEmpty())
                <canvas id="enrollmentsByStatusChart" height="120"></canvas>
            @else
                <p class="text-sm text-gray-400">لا توجد بيانات</p>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">النشاط المالي الشهري (آخر 24 شهراً)</h2>
            @if ($financialActivity->isNotEmpty())
                <canvas id="financialChart" height="160"></canvas>
            @else
                <p class="text-sm text-gray-400">لا توجد بيانات</p>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">أعلى التصنيفات إنفاقاً</h2>
            @if ($topCategories->isNotEmpty())
                <canvas id="topCategoriesChart" height="160"></canvas>
            @else
                <p class="text-sm text-gray-400">لا توجد بيانات</p>
            @endif
        </section>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">أكثر الدورات تسجيلاً</h2>
            <div class="space-y-3">
                @forelse ($enrollmentsByCourse as $course)
                    <div class="flex items-center justify-between border-b border-gray-50 pb-2 last:border-0">
                        <p class="text-sm text-gray-800">{{ $course->title }}</p>
                        <span class="text-sm font-bold text-gray-600">{{ $course->enrollments_count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">لا توجد بيانات</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-gray-900">الاختبارات</h2>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $quizStats['attempts'] }}</p>
                    <p class="text-xs text-gray-500">محاولة</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-green-600">{{ $quizStats['passed'] }}</p>
                    <p class="text-xs text-gray-500">ناجحة</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-primary-600">{{ $quizStats['pass_rate'] }}%</p>
                    <p class="text-xs text-gray-500">نسبة النجاح</p>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
@if ($usersByRole->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.Chart === 'undefined') return;

        new Chart(document.getElementById('usersByRoleChart'), {
            type: 'pie',
            data: {
                labels: @json($usersByRole->keys()->map(fn ($r) => $r === 'admin' ? 'مدير' : 'مستخدم')),
                datasets: [{ data: @json($usersByRole->values()), backgroundColor: ['#1c80f1', '#e5e7eb'] }],
            },
        });

        new Chart(document.getElementById('enrollmentsByStatusChart'), {
            type: 'pie',
            data: {
                labels: @json($enrollmentsByStatus->keys()),
                datasets: [{ data: @json($enrollmentsByStatus->values()), backgroundColor: ['#1c80f1', '#d4af37', '#f97316'] }],
            },
        });

        const months = @json($financialActivity->pluck('month')->unique()->values());
        const income = months.map(m => @json($financialActivity->where('type', 'income')->pluck('total', 'month'))[m] ?? 0);
        const expense = months.map(m => @json($financialActivity->where('type', 'expense')->pluck('total', 'month'))[m] ?? 0);

        new Chart(document.getElementById('financialChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    { label: 'مداخيل', data: income, backgroundColor: '#22c55e' },
                    { label: 'مصاريف', data: expense, backgroundColor: '#ef4444' },
                ],
            },
            options: { responsive: true, scales: { x: { stacked: false } } },
        });

        new Chart(document.getElementById('topCategoriesChart'), {
            type: 'bar',
            data: {
                labels: @json($topCategories->pluck('name')),
                datasets: [{ label: 'إجمالي الإنفاق', data: @json($topCategories->pluck('total')), backgroundColor: '#1c80f1' }],
            },
            options: { indexAxis: 'y', responsive: true },
        });
    });
</script>
@endif
@endpush
