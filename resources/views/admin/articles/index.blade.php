@extends('layouts.admin')

@section('title', 'المقالات')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">إدارة المقالات</h1>
            <p class="mt-1 text-sm text-gray-500">نشر وإدارة المحتوى التثقيفي</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
            + مقال جديد
        </a>
    </div>

    <form method="GET" class="mb-6 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..."
            class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
        <label class="flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm">
            <input type="checkbox" name="published" value="1" @checked(request('published')) class="h-4 w-4 text-primary-600"> منشورة فقط
        </label>
        <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700">تصفية</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-right text-xs text-gray-500">
                    <th class="px-4 py-3 font-medium">العنوان</th>
                    <th class="px-4 py-3 font-medium">التصنيف</th>
                    <th class="px-4 py-3 font-medium">المشاهدات</th>
                    <th class="px-4 py-3 font-medium">الحالة</th>
                    <th class="px-4 py-3 font-medium">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($articles as $article)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $article->title }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $article->category->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $article->views_count }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full {{ $article->is_published ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }} px-2.5 py-0.5 text-xs font-medium">
                                {{ $article->is_published ? 'منشور' : 'مسودة' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="text-amber-600 hover:underline">تعديل</a>
                            <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" class="inline" onsubmit="return confirm('حذف المقال؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mr-2 text-red-600 hover:underline">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">لا توجد مقالات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $articles->links() }}</div>
@endsection
