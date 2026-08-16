@extends('layouts.app')

@section('title', 'تعديل معاملة')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-gray-900">تعديل المعاملة</h1>

        <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div>
                <label for="type" class="mb-1.5 block text-sm font-medium text-gray-700">نوع المعاملة</label>
                <select id="type" name="type" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="expense" @selected(old('type', $transaction->type) === 'expense')>مصروف</option>
                    <option value="income" @selected(old('type', $transaction->type) === 'income')>دخل</option>
                    <option value="transfer" @selected(old('type', $transaction->type) === 'transfer')>تحويل</option>
                </select>
            </div>

            <div>
                <label for="account_id" class="mb-1.5 block text-sm font-medium text-gray-700">الحساب</label>
                <select id="account_id" name="account_id" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" @selected(old('account_id', $transaction->account_id) == $account->id)>{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ</label>
                <input id="amount" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">التصنيف</label>
                <select id="category_id" name="category_id"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">—</option>
                    <optgroup label="المداخيل">
                        @foreach ($incomeCategories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $transaction->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="المصاريف">
                        @foreach ($expenseCategories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $transaction->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">الوصف</label>
                <input id="description" type="text" name="description" value="{{ old('description', $transaction->description) }}" maxlength="255"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="transaction_date" class="mb-1.5 block text-sm font-medium text-gray-700">التاريخ</label>
                <input id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', optional($transaction->transaction_date)->format('Y-m-d')) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="recurrence_end_date" class="mb-1.5 block text-sm font-medium text-gray-700">نهاية التكرار (اختياري)</label>
                <input id="recurrence_end_date" type="date" name="recurrence_end_date" value="{{ old('recurrence_end_date', optional($transaction->recurrence_end_date)->format('Y-m-d')) }}"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                حفظ التعديلات
            </button>
        </form>
    </div>
@endsection
