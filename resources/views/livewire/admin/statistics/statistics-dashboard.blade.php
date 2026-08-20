<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">الإحصائيات التحليلية</h1>
    </div>

    <div wire:ignore class="grid grid-cols-1 gap-6 lg:grid-cols-2">
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

    @script
    <script>
        (function () {
            const chartData = @json($chartData);

            function renderChart(canvasId, config) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof window.Chart === 'undefined') {
                    return;
                }

                const existing = window.Chart.getChart(canvas);
                if (existing) {
                    existing.destroy();
                }

                new window.Chart(canvas, config);
            }

            if (chartData.usersByRole.data.length) {
                renderChart('usersByRoleChart', {
                    type: 'pie',
                    data: {
                        labels: chartData.usersByRole.labels,
                        datasets: [{ data: chartData.usersByRole.data, backgroundColor: ['#1c80f1', '#e5e7eb'] }],
                    },
                });
            }

            if (chartData.enrollmentsByStatus.data.length) {
                renderChart('enrollmentsByStatusChart', {
                    type: 'pie',
                    data: {
                        labels: chartData.enrollmentsByStatus.labels,
                        datasets: [{ data: chartData.enrollmentsByStatus.data, backgroundColor: ['#1c80f1', '#d4af37', '#f97316'] }],
                    },
                });
            }

            // One currency never gets summed into another's total (no FX
            // conversion exists in this app) — each currency present in the
            // data gets its own income/expense dataset pair, built here in
            // PHP and only rendered by Chart.js. The color palettes below
            // keep the first (and, for the common single-currency case,
            // only) currency visually identical to the original chart.
            const incomeColors = ['#22c55e', '#4ade80', '#15803d', '#86efac', '#059669'];
            const expenseColors = ['#ef4444', '#f87171', '#b91c1c', '#fca5a5', '#dc2626'];
            const categoryColors = ['#1c80f1', '#d4af37', '#22c55e', '#a855f7', '#f97316'];

            if (chartData.financialActivity.months.length) {
                const datasets = [];
                chartData.financialActivity.series.forEach((series, i) => {
                    datasets.push({
                        label: `مداخيل — ${series.currency}`,
                        data: series.income,
                        backgroundColor: incomeColors[i % incomeColors.length],
                    });
                    datasets.push({
                        label: `مصاريف — ${series.currency}`,
                        data: series.expense,
                        backgroundColor: expenseColors[i % expenseColors.length],
                    });
                });

                renderChart('financialChart', {
                    type: 'bar',
                    data: {
                        labels: chartData.financialActivity.months,
                        datasets: datasets,
                    },
                    options: { responsive: true, scales: { x: { stacked: false } } },
                });
            }

            if (chartData.topCategories.labels.length) {
                const datasets = chartData.topCategories.series.map((series, i) => ({
                    label: series.currency,
                    data: series.data,
                    backgroundColor: categoryColors[i % categoryColors.length],
                }));

                renderChart('topCategoriesChart', {
                    type: 'bar',
                    data: {
                        labels: chartData.topCategories.labels,
                        datasets: datasets,
                    },
                    options: { indexAxis: 'y', responsive: true },
                });
            }
        })();
    </script>
    @endscript
</div>
