@extends('layouts.admin')

@section('title', 'تعديل دورة')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-extrabold text-gray-900">تعديل الدورة</h1>

        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700">عنوان الدورة</label>
                <input id="title" type="text" name="title" value="{{ old('title', $course->title) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">الوصف</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="level" class="mb-1.5 block text-sm font-medium text-gray-700">المستوى</label>
                    <select id="level" name="level" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <option value="beginner" @selected(old('level', $course->level) === 'beginner')>مبتدئ</option>
                        <option value="intermediate" @selected(old('level', $course->level) === 'intermediate')>متوسط</option>
                        <option value="advanced" @selected(old('level', $course->level) === 'advanced')>متقدم</option>
                    </select>
                </div>
                <div>
                    <label for="duration_hours" class="mb-1.5 block text-sm font-medium text-gray-700">المدة بالساعات</label>
                    <input id="duration_hours" type="number" min="0" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="passing_score" class="mb-1.5 block text-sm font-medium text-gray-700">درجة النجاح (%)</label>
                    <input id="passing_score" type="number" min="0" max="100" name="passing_score" value="{{ old('passing_score', $course->passing_score) }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="age_groups" class="mb-1.5 block text-sm font-medium text-gray-700">الفئات العمرية</label>
                    <select id="age_groups" name="age_groups[]" multiple
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        @foreach ($ageGroups as $ageGroup)
                            <option value="{{ $ageGroup->id }}" @selected(in_array($ageGroup->id, old('age_groups', $course->ageGroups->pluck('id')->all())))>{{ $ageGroup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="thumbnail" class="mb-1.5 block text-sm font-medium text-gray-700">الصورة المصغرة</label>
                    <input id="thumbnail" type="file" name="thumbnail" accept="image/*"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-primary-700">
                </div>
            </div>

            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $course->is_published)) class="h-4 w-4 text-primary-600"> نشر الدورة
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="certificate_enabled" value="1" @checked(old('certificate_enabled', $course->certificate_enabled)) class="h-4 w-4 text-primary-600"> تفعيل شهادة الإتمام
                </label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white hover:bg-primary-700">حفظ التعديلات</button>
        </form>
    </div>
@endsection
