@extends('layouts.admin')

@section('title', 'المستخدمون')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">إدارة المستخدمين</h1>
            <p class="mt-1 text-sm text-gray-500">عرض وتعديل حسابات المستخدمين</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد..."
                class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
            <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700">بحث</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-right text-xs text-gray-500">
                    <th class="px-4 py-3 font-medium">المستخدم</th>
                    <th class="px-4 py-3 font-medium">الدور</th>
                    <th class="px-4 py-3 font-medium">المستوى المالي</th>
                    <th class="px-4 py-3 font-medium">الفئة العمرية</th>
                    <th class="px-4 py-3 font-medium">الانضمام</th>
                    <th class="px-4 py-3 font-medium">الحالة</th>
                    <th class="px-4 py-3 font-medium">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-600' }} px-2.5 py-0.5 text-xs font-medium">
                                {{ $user->role === 'admin' ? 'مدير' : 'مستخدم' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->financial_level ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->ageGroup?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }} px-2.5 py-0.5 text-xs font-medium">
                                {{ $user->is_active ? 'نشط' : 'موقوف' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-primary-600 hover:underline">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">لا يوجد مستخدمون</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
