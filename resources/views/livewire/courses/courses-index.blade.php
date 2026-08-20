<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">الدورات التدريبية</h1>
            <p class="mt-1 text-sm text-gray-500">تعلّم أساسيات الثقافة المالية خطوة بخطوة</p>
        </div>
        <a href="{{ route('courses.recommended') }}" class="btn-gold">الدورات الموصى بها لك</a>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:grid-cols-3">
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">بحث</label>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="ابحث عن دورة..."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">المستوى</label>
            <select wire:model.live="level" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">الكل</option>
                <option value="beginner">مبتدئ</option>
                <option value="intermediate">متوسط</option>
                <option value="advanced">متقدم</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">الفئة العمرية</label>
            <select wire:model.live="age_group_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">الكل</option>
                @foreach ($ageGroups as $ageGroup)
                    <option value="{{ $ageGroup->id }}">{{ $ageGroup->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $course)
            <a href="{{ route('courses.show', $course) }}" wire:key="course-{{ $course->id }}" class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                <div class="flex h-36 items-center justify-center bg-brand-gradient text-4xl">
                    {{ $course->thumbnail ? '' : '📚' }}
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700">
                            {{ match ($course->level) { 'beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم', default => $course->level } }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $course->duration_hours }} ساعة</span>
                    </div>
                    <h2 class="mt-2 font-bold text-gray-900 group-hover:text-primary-700">{{ $course->title }}</h2>
                    <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $course->description }}</p>
                    <p class="mt-3 text-xs text-gray-400">{{ $course->modules_count ?? $course->modules->count() }} وحدة · {{ $course->enrollments_count ?? 0 }} متعلم</p>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-500">لا توجد دورات مطابقة</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>
</div>
