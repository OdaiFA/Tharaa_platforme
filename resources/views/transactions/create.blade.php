@extends('layouts.app')

@section('title', 'إضافة معاملة')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-gray-900">إضافة معاملة</h1>
        <p class="mt-1 text-sm text-gray-500">سجل عملية مالية جديدة</p>

        <form method="POST" action="{{ route('transactions.store') }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">نوع المعاملة</label>
                <div class="grid grid-cols-3 gap-2" x-data="{ type: '{{ old('type', 'expense') }}' }">
                    <button type="button" @click="type = 'expense'" :class="type === 'expense' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 bg-white text-gray-600'"
                        class="rounded-xl border px-4 py-2.5 text-sm font-bold transition">مصروف</button>
                    <button type="button" @click="type = 'income'" :class="type === 'income' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-600'"
                        class="rounded-xl border px-4 py-2.5 text-sm font-bold transition">دخل</button>
                    <button type="button" @click="type = 'transfer'" :class="type === 'transfer' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-600'"
                        class="rounded-xl border px-4 py-2.5 text-sm font-bold transition">تحويل</button>
                    <input type="hidden" name="type" :value="type">
                </div>
            </div>

            <div>
                <label for="account_id" class="mb-1.5 block text-sm font-medium text-gray-700">الحساب</label>
                <select id="account_id" name="account_id" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->name }} ({{ $account->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ</label>
                <input id="amount" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div id="category-field">
                <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">التصنيف</label>
                <select id="category_id" name="category_id" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <optgroup label="المداخيل">
                        @foreach ($incomeCategories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="المصاريف">
                        @foreach ($expenseCategories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">الوصف</label>
                <input id="description" type="text" name="description" value="{{ old('description') }}" maxlength="255"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="transaction_date" class="mb-1.5 block text-sm font-medium text-gray-700">التاريخ</label>
                <input id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div x-data="{ recurring: false }">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" x-model="recurring" name="is_recurring" value="1" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    معاملة متكررة
                </label>
                <div x-show="recurring" x-cloak class="mt-3 grid grid-cols-2 gap-3">
                    <div>
                        <label for="recurrence_type" class="mb-1.5 block text-sm font-medium text-gray-700">التكرار</label>
                        <select id="recurrence_type" name="recurrence_type" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="daily">يومي</option>
                            <option value="weekly">أسبوعي</option>
                            <option value="monthly">شهري</option>
                            <option value="yearly">سنوي</option>
                        </select>
                    </div>
                    <div>
                        <label for="recurrence_end_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ النهاية</label>
                        <input id="recurrence_end_date" type="date" name="recurrence_end_date" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                حفظ المعاملة
            </button>
        </form>
    </div>
@endsection
