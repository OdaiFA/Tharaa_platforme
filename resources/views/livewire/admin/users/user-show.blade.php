<div>
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-primary-600 hover:underline">← كل المستخدمين</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">{{ $user->name }}</h1>
        <p class="text-sm text-gray-500">{{ $user->email }}</p>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">إجمالي الأرصدة</p>
            @forelse ($totals['balanceByCurrency'] as $currency => $amount)
                <p class="mt-1 text-xl font-extrabold text-gray-900">{{ number_format($amount, 2) }} <span class="text-sm font-medium text-gray-400">{{ $currency }}</span></p>
            @empty
                <p class="mt-1 text-xl font-extrabold text-gray-900">0.00</p>
            @endforelse
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">المعاملات</p>
            <p class="mt-1 text-xl font-extrabold text-gray-900">{{ $totals['transactions'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">الدورات المسجلة</p>
            <p class="mt-1 text-xl font-extrabold text-gray-900">{{ $totals['enrollments'] }}</p>
        </div>
    </div>
</div>
