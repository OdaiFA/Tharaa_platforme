@extends('layouts.admin')

@section('title', 'تعديل مقال')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-extrabold text-gray-900">تعديل المقال</h1>

        <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700">العنوان</label>
                <input id="title" type="text" name="title" value="{{ old('title', $article->title) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-gray-700">المقتطف (اختياري)</label>
                <textarea id="excerpt" name="excerpt" rows="2" maxlength="500"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">{{ old('excerpt', $article->excerpt) }}</textarea>
            </div>

            <div>
                <label for="content" class="mb-1.5 block text-sm font-medium text-gray-700">المحتوى (يدعم Markdown)</label>
                <textarea id="content" name="content" rows="10" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">{{ old('content', $article->content) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">التصنيف</label>
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <option value="">—</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $article->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="featured_image" class="mb-1.5 block text-sm font-medium text-gray-700">الصورة المميزة</label>
                    <input id="featured_image" type="file" name="featured_image" accept="image/*"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-primary-700">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published)) class="h-4 w-4 text-primary-600"> نشر المقال
            </label>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white hover:bg-primary-700">حفظ التعديلات</button>
        </form>
    </div>
@endsection
