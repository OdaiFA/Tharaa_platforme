@extends('layouts.admin')

@section('title', 'الفئات العمرية')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">الفئات العمرية</h1>
        <p class="mt-1 text-sm text-gray-500">تُستخدم لتخصيص الدورات والمحتوى</p>
    </div>

    <form method="POST" action="{{ route('admin.age-groups.store') }}" class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm md:grid-cols-4">
        @csrf
        <input type="text" name="name" placeholder="اسم الفئة (مثال: أطفال 7-12)" required
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <input type="number" name="min_age" placeholder="الحد الأدنى للعمر" min="0" required
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <input type="number" name="max_age" placeholder="الحد الأقصى للعمر" min="0" required
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">+ إضافة</button>
    </form>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($ageGroups as $ageGroup)
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $ageGroup->name }}</p>
                        <p class="text-xs text-gray-400">{{ $ageGroup->min_age }} — {{ $ageGroup->max_age }} سنة · {{ $ageGroup->users_count }} مستخدم</p>
                    </div>
                    <button onclick="document.getElementById('edit-{{ $ageGroup->id }}').classList.toggle('hidden')"
                        class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200">تعديل</button>
                </div>
                <form id="edit-{{ $ageGroup->id }}" method="POST" action="{{ route('admin.age-groups.update', $ageGroup) }}" class="mt-3 hidden space-y-2 border-t border-gray-100 pt-3">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $ageGroup->name }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <div class="flex gap-2">
                        <input type="number" name="min_age" value="{{ $ageGroup->min_age }}" min="0" required
                            class="w-1/2 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                        <input type="number" name="max_age" value="{{ $ageGroup->max_age }}" min="0" required
                            class="w-1/2 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-primary-600 py-2 text-sm font-bold text-white hover:bg-primary-700">حفظ</button>
                </form>
            </div>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-400">لا توجد فئات عمرية</p>
        @endforelse
    </div>
@endsection
