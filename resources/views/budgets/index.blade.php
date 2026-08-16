@extends('layouts.app')

@section('title', 'الميزانيات')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">الميزانيات</h1>
            <p class="mt-1 text-sm text-gray-500">حدد سقفاً لإنفاقك وتابع التزامك به</p>
        </div>
        <a href="{{ route('budgets.create') }}" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
            + ميزانية جديدة
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($budgets as $item)
            @php $budget = $item['budget']; $consumption = $item['consumption']; @endphp
            <a href="{{ route('budgets.show', $budget) }}" class="group rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="font-bold text-gray-900 group-hover:text-primary-700">{{ $budget->name }}</h2>
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ $budget->start_date->translatedFormat('d M') }} — {{ $budget->end_date->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">{{ $consumption['percentage'] }}%</span>
                </div>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full {{ $consumption['percentage'] >= 100 ? 'bg-red-500' : ($consumption['percentage'] >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                        style="width: {{ $consumption['percentage'] }}%"></div>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">أنفقت <b class="text-gray-800">{{ number_format($consumption['spent'], 2) }}</b></span>
                    <span class="text-gray-400">من {{ number_format($budget->total_amount, 2) }} {{ $budget->currency }}</span>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-500">لا توجد ميزانيات نشطة</p>
                <a href="{{ route('budgets.create') }}" class="mt-3 inline-block text-sm font-bold text-primary-600 hover:underline">أنشئ ميزانية</a>
            </div>
        @endforelse
    </div>
@endsection
