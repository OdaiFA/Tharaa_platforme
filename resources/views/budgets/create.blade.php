@extends('layouts.app')

@section('title', 'إنشاء ميزانية')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-extrabold text-gray-900">إنشاء ميزانية</h1>
        <p class="mt-1 text-sm text-gray-500">حدد الحدود الشهرية لإنفاقك حسب التصنيفات</p>

        <form method="POST" action="{{ route('budgets.store') }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm" x-data="{ categories: [] }">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الميزانية</label>
                    <input id="name" type="text" name="name" value="{{ old('name', 'ميزانية ' . now()->translatedFormat('F Y')) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="total_amount" class="mb-1.5 block text-sm font-medium text-gray-700">الإجمالي</label>
                    <input id="total_amount" type="number" step="0.01" min="1" name="total_amount" value="{{ old('total_amount') }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ البداية</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ النهاية</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', now()->endOfMonth()->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="alert_percentage" class="mb-1.5 block text-sm font-medium text-gray-700">نسبة التنبيه الافتراضية (%)</label>
                    <input id="alert_percentage" type="number" min="1" max="100" name="alert_percentage" value="{{ old('alert_percentage', 80) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
                    <input id="currency" type="text" name="currency" value="{{ old('currency', auth()->user()->currency) }}" maxlength="3" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-gray-200 p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-700">حدود التصنيفات (اختياري)</h2>
                    <button type="button" @click="categories.push({ category_id: '', limit_amount: '', alert_percentage: 80 })"
                        class="rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-bold text-primary-700 hover:bg-primary-100">
                        + إضافة تصنيف
                    </button>
                </div>

                <template x-for="(category, index) in categories" :key="index">
                    <div class="mb-3 grid grid-cols-12 gap-2 rounded-lg bg-gray-50 p-3">
                        <select :name="`categories[${index}][category_id]`" x-model="category.category_id" required
                            class="col-span-12 rounded-lg border border-gray-300 px-3 py-2 text-sm sm:col-span-5 focus:border-primary-500 focus:outline-none">
                            <option value="">اختر التصنيف</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <input :name="`categories[${index}][limit_amount]`" x-model="category.limit_amount" type="number" step="0.01" min="1" placeholder="الحد الأقصى"
                            required class="col-span-6 rounded-lg border border-gray-300 px-3 py-2 text-sm sm:col-span-4 focus:border-primary-500 focus:outline-none">
                        <input :name="`categories[${index}][alert_percentage]`" x-model="category.alert_percentage" type="number" min="1" max="100" placeholder="نسبة التنبيه"
                            class="col-span-6 rounded-lg border border-gray-300 px-3 py-2 text-sm sm:col-span-2 focus:border-primary-500 focus:outline-none">
                        <button type="button" @click="categories.splice(index, 1)" class="col-span-12 text-right text-xs font-bold text-red-500 hover:underline sm:col-span-1">
                            إزالة
                        </button>
                    </div>
                </template>

                <p x-show="categories.length === 0" class="text-center text-xs text-gray-400">لا توجد تصنيفات مضافة — حدد حدوداً لكل تصنيف إن أردت</p>
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                إنشاء الميزانية
            </button>
        </form>
    </div>
@endsection
