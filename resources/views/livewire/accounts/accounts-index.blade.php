<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">الحسابات المالية</h1>
            <p class="mt-1 text-sm text-gray-500">إدارة حساباتك البنكية والنقدية</p>
        </div>
        <a href="{{ route('accounts.create') }}" class="btn-primary">+ حساب جديد</a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($accounts as $account)
            <div wire:key="account-{{ $account->id }}" class="bg-financial-card relative overflow-hidden p-5 text-white">
                <div class="pointer-events-none absolute -top-8 -left-8 h-24 w-24 rounded-full bg-gold-500/15 blur-2xl"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-300">{{ $account->name }}</p>
                        <p class="font-num mt-3 text-2xl font-bold">{{ number_format($account->balance, 2) }} {{ auth()->user()->currency }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $account->type }} · {{ $account->transactions_count }} معاملة</p>
                    </div>
                    <span class="h-2 w-2 rounded-full {{ $account->is_active ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                </div>
                <div class="relative mt-4 flex items-center justify-end gap-2 border-t border-white/10 pt-3 text-xs">
                    @if ($confirmingDeleteId === $account->id)
                        <span class="font-medium text-red-200">حذف الحساب؟</span>
                        <button type="button" wire:click="delete({{ $account->id }})" wire:loading.attr="disabled" wire:target="delete({{ $account->id }})"
                            class="rounded-lg bg-red-500/40 px-3 py-1.5 font-bold text-white hover:bg-red-500/60 disabled:opacity-60">نعم، احذف</button>
                        <button type="button" wire:click="cancelDelete" class="rounded-lg bg-white/10 px-3 py-1.5 font-medium hover:bg-white/20">إلغاء</button>
                    @else
                        <a href="{{ route('accounts.edit', $account) }}" class="rounded-lg bg-white/10 px-3 py-1.5 font-medium hover:bg-white/20">تعديل</a>
                        <button type="button" wire:click="confirmDelete({{ $account->id }})" class="rounded-lg bg-red-500/20 px-3 py-1.5 font-medium text-red-100 hover:bg-red-500/40">حذف</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-500">لا توجد حسابات بعد</p>
                <a href="{{ route('accounts.create') }}" class="mt-3 inline-block text-sm font-bold text-primary-600 hover:underline">أنشئ حسابك الأول</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $accounts->links() }}</div>
</div>
