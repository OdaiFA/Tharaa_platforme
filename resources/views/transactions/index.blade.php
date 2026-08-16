@extends('layouts.app')

@section('title', 'المعاملات')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">المعاملات المالية</h1>
            <p class="mt-1 text-sm text-gray-500">سجل كل مصروفاتك ومداخيلك وتحويلاتك</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
            + معاملة جديدة
        </a>
    </div>

    <form method="GET" class="mb-6 grid grid-cols-2 gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-5">
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">النوع</label>
            <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">الكل</option>
                <option value="income" @selected(request('type') === 'income')>دخل</option>
                <option value="expense" @selected(request('type') === 'expense')>مصروف</option>
                <option value="transfer" @selected(request('type') === 'transfer')>تحويل</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">الحساب</label>
            <select name="account_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">الكل</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">من تاريخ</label>
            <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">إلى تاريخ</label>
            <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700">تصفية</button>
            <a href="{{ route('transactions.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">مسح</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-right text-xs text-gray-500">
                    <th class="px-4 py-3 font-medium">التاريخ</th>
                    <th class="px-4 py-3 font-medium">الوصف</th>
                    <th class="px-4 py-3 font-medium">الحساب</th>
                    <th class="px-4 py-3 font-medium">التصنيف</th>
                    <th class="px-4 py-3 font-medium">النوع</th>
                    <th class="px-4 py-3 font-medium">المبلغ</th>
                    <th class="px-4 py-3 font-medium">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($transactions as $transaction)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $transaction->description ?: '—' }}</p>
                            @if ($transaction->is_recurring)
                                <span class="text-xs text-primary-600">🔁 متكررة ({{ $transaction->recurrence_type }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $transaction->account->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $transaction->category->name ?? ($transaction->transferToAccount ? 'تحويل إلى ' . $transaction->transferToAccount->name : '—') }}</td>
                        <td class="px-4 py-3">
                            @if ($transaction->type === 'income')
                                <span class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">دخل</span>
                            @elseif ($transaction->type === 'expense')
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">مصروف</span>
                            @else
                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">تحويل</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold {{ $transaction->type === 'income' ? 'text-green-600' : ($transaction->type === 'expense' ? 'text-red-600' : 'text-blue-600') }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('transactions.edit', $transaction) }}" class="text-primary-600 hover:underline">تعديل</a>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="inline" onsubmit="return confirm('حذف هذه المعاملة؟ سيُعاد احتساب الرصيد')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mr-2 text-red-600 hover:underline">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">لا توجد معاملات مطابقة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $transactions->links() }}</div>
@endsection
