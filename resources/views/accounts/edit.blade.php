@extends('layouts.app')

@section('title', 'تعديل حساب')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-gray-900">تعديل الحساب</h1>

        <form method="POST" action="{{ route('accounts.update', $account) }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الحساب</label>
                <input id="name" type="text" name="name" value="{{ old('name', $account->name) }}" required autofocus
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="type" class="mb-1.5 block text-sm font-medium text-gray-700">نوع الحساب</label>
                <select id="type" name="type" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="bank" @selected(old('type', $account->type) === 'bank')>حساب بنكي</option>
                    <option value="cash" @selected(old('type', $account->type) === 'cash')>نقدي</option>
                    <option value="electronic" @selected(old('type', $account->type) === 'electronic')>محفظة إلكترونية</option>
                </select>
            </div>

            <div>
                <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
                <input id="currency" type="text" name="currency" value="{{ old('currency', $account->currency) }}" maxlength="3"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">وصف (اختياري)</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">{{ old('description', $account->description) }}</textarea>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked($account->is_active) class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                حساب نشط
            </label>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                حفظ التعديلات
            </button>
        </form>
    </div>
@endsection
