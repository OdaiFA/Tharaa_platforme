<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">المعاملات المالية</h1>
            <p class="mt-1 text-sm text-gray-500">سجل كل مصروفاتك ومداخيلك وتحويلاتك</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
            + معاملة جديدة
        </a>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-5">
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">النوع</label>
            <select wire:model.live="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">الكل</option>
                <option value="income">دخل</option>
                <option value="expense">مصروف</option>
                <option value="transfer">تحويل</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">الحساب</label>
            <select wire:model.live="account_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">الكل</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">من تاريخ</label>
            <input type="date" wire:model.live="from" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">إلى تاريخ</label>
            <input type="date" wire:model.live="to" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div class="flex items-end gap-2">
            <button type="button" wire:click="clearFilters" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">مسح</button>
        </div>
    </div>

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
                    <tr wire:key="transaction-{{ $transaction->id }}" class="hover:bg-gray-50/50">
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
                            @if ($confirmingDeleteId === $transaction->id)
                                <span class="text-xs font-medium text-red-600">حذف؟</span>
                                <button type="button" wire:click="delete({{ $transaction->id }})" wire:loading.attr="disabled" wire:target="delete({{ $transaction->id }})"
                                    class="mr-1 text-red-600 hover:underline disabled:opacity-60">نعم</button>
                                <button type="button" wire:click="cancelDelete" class="mr-1 text-gray-500 hover:underline">إلغاء</button>
                            @else
                                <a href="{{ route('transactions.edit', $transaction) }}" class="text-primary-600 hover:underline">تعديل</a>
                                <button type="button" wire:click="confirmDelete({{ $transaction->id }})" class="mr-2 text-red-600 hover:underline">حذف</button>
                            @endif
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
</div>
