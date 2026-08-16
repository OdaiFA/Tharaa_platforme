@extends('layouts.app')

@section('title', $budget->name)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $budget->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $budget->start_date->translatedFormat('d M Y') }} — {{ $budget->end_date->translatedFormat('d M Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('budgets.edit', $budget) }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">تعديل</a>
            <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('حذف هذه الميزانية؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-100">حذف</button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">إجمالي الميزانية</p>
                <p class="mt-1 text-3xl font-extrabold text-gray-900">{{ number_format($budget->total_amount, 2) }} {{ $budget->currency }}</p>
            </div>
            <div class="text-left">
                <p class="text-sm text-gray-500">المتبقي</p>
                <p class="mt-1 text-3xl font-extrabold {{ $consumption['remaining'] <= 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($consumption['remaining'], 2) }}</p>
            </div>
        </div>
        <div class="mt-5 h-3 overflow-hidden rounded-full bg-gray-100">
            <div class="h-full rounded-full {{ $consumption['percentage'] >= 100 ? 'bg-red-500' : ($consumption['percentage'] >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                style="width: {{ $consumption['percentage'] }}%"></div>
        </div>
        <p class="mt-2 text-xs text-gray-400">أنفقت {{ number_format($consumption['spent'], 2) }} من {{ number_format($budget->total_amount, 2) }} ({{ $consumption['percentage'] }}%)</p>
    </div>

    <h2 class="mt-8 mb-3 font-bold text-gray-900">حدود التصنيفات</h2>
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        @forelse ($categories as $item)
            @php $bc = $item['budget_category']; @endphp
            <div class="border-b border-gray-50 p-4 last:border-0">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-800">{{ $bc->category->name }}</span>
                    <span class="text-sm text-gray-500">
                        <b class="{{ $item['spent'] > $item['limit'] ? 'text-red-600' : 'text-gray-800' }}">{{ number_format($item['spent'], 2) }}</b>
                        / {{ number_format($item['limit'], 2) }}
                    </span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full {{ $item['percentage'] >= 100 ? 'bg-red-500' : ($item['percentage'] >= $bc->alert_percentage ? 'bg-amber-500' : 'bg-primary-500') }}"
                        style="width: {{ $item['percentage'] }}%"></div>
                </div>
                <p class="mt-1 text-xs text-gray-400">نسبة التنبيه: {{ $bc->alert_percentage }}%</p>
            </div>
        @empty
            <p class="p-8 text-center text-sm text-gray-400">لم تُحدد حدود للتصنيفات في هذه الميزانية</p>
        @endforelse
    </div>
@endsection
