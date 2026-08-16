@extends('layouts.app')

@section('title', 'الأهداف')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">أهداف الادخار</h1>
            <p class="mt-1 text-sm text-gray-500">حدد أهدافك المالية وتابع تحقيقها</p>
        </div>
        <a href="{{ route('goals.create') }}" class="btn-primary">+ هدف جديد</a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($goals as $goal)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold-500/10 text-xl">{{ $goal->icon ?: '🎯' }}</span>
                        <div>
                            <h2 class="font-bold text-gray-900">{{ $goal->name }}</h2>
                            <p class="text-xs text-gray-400">
                                @if ($goal->deadline)
                                    الموعد النهائي: {{ $goal->deadline->translatedFormat('d M Y') }}
                                @else
                                    بدون موعد نهائي
                                @endif
                            </p>
                        </div>
                    </div>
                    @if ($goal->progress_percentage >= 100)
                        <span class="rounded-full bg-gold-50 px-2 py-0.5 text-xs font-bold text-gold-700">🏆 مكتمل</span>
                    @endif
                </div>

                <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full {{ $goal->progress_percentage >= 100 ? 'bg-green-500' : 'bg-gold-gradient' }} transition-all"
                        style="width: {{ min($goal->progress_percentage, 100) }}%"></div>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">
                        <b class="text-gray-800">{{ number_format($goal->current_amount, 2) }}</b> / {{ number_format($goal->target_amount, 2) }} {{ auth()->user()->currency }}
                    </span>
                    <span class="font-bold text-gray-600">{{ $goal->progress_percentage }}%</span>
                </div>

                <div class="mt-4 flex items-center gap-2 border-t border-gray-50 pt-3">
                    <button @click="$refs.contribute{{ $goal->id }}.showModal()"
                        class="btn-gold flex-1">إضافة مساهمة</button>
                    <a href="{{ route('goals.edit', $goal) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">تعديل</a>
                    <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('حذف هذا الهدف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-red-100 px-3 py-2 text-sm text-red-600 hover:bg-red-50">حذف</button>
                    </form>
                </div>
            </div>

            <dialog x-ref="contribute{{ $goal->id }}" class="rounded-2xl border-0 p-0 shadow-2xl" @click.self="close()">
                <form method="POST" action="{{ route('goals.contribute', $goal) }}" class="w-80 max-w-[calc(100vw-2rem)] p-6">
                    @csrf
                    <h3 class="font-bold text-gray-900">مساهمة في «{{ $goal->name }}»</h3>
                    <p class="mt-0.5 text-xs text-gray-500">المتبقي: {{ number_format(max($goal->target_amount - $goal->current_amount, 0), 2) }} {{ auth()->user()->currency }}</p>

                    <label class="mt-4 mb-1.5 block text-sm font-medium text-gray-700">المبلغ</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required autofocus
                        class="input-field">

                    <label class="mt-3 mb-1.5 block text-sm font-medium text-gray-700">من الحساب</label>
                    <select name="account_id" class="input-field">
                        <option value="">—</option>
                        @foreach (auth()->user()->accounts()->active()->get() as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>

                    <label class="mt-3 mb-1.5 block text-sm font-medium text-gray-700">ملاحظة (اختياري)</label>
                    <input type="text" name="note" maxlength="255" class="input-field">

                    <div class="mt-5 flex gap-2">
                        <button type="submit" class="btn-gold flex-1">إضافة</button>
                        <button type="button" @click="$refs.contribute{{ $goal->id }}.close()" class="btn-outline">إلغاء</button>
                    </div>
                </form>
            </dialog>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-500">لا توجد أهداف بعد</p>
                <a href="{{ route('goals.create') }}" class="mt-3 inline-block text-sm font-bold text-primary-600 hover:underline">أنشئ هدفاً جديداً</a>
            </div>
        @endforelse
    </div>
@endsection
