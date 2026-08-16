@extends('layouts.app')

@section('title', 'تعديل ميزانية')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-extrabold text-gray-900">تعديل الميزانية</h1>

        <form method="POST" action="{{ route('budgets.update', $budget) }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الميزانية</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $budget->name) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="total_amount" class="mb-1.5 block text-sm font-medium text-gray-700">الإجمالي</label>
                    <input id="total_amount" type="number" step="0.01" min="1" name="total_amount" value="{{ old('total_amount', $budget->total_amount) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ البداية</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', optional($budget->start_date)->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ النهاية</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', optional($budget->end_date)->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="alert_percentage" class="mb-1.5 block text-sm font-medium text-gray-700">نسبة التنبيه الافتراضية (%)</label>
                    <input id="alert_percentage" type="number" min="1" max="100" name="alert_percentage" value="{{ old('alert_percentage', $budget->alert_percentage) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
                    <input id="currency" type="text" name="currency" value="{{ old('currency', $budget->currency) }}" maxlength="3" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                حفظ التعديلات
            </button>
        </form>
    </div>
@endsection
