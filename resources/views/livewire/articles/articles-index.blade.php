<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">المقالات التثقيفية</h1>
        <p class="mt-1 text-sm text-gray-500">محتوى مالي مبسط لتعزيز ثقافتك</p>
    </div>

    @if ($categories->isNotEmpty())
        <div class="mb-6 flex flex-wrap gap-2">
            <button type="button" wire:click="$set('category_id', '')"
                class="rounded-full px-4 py-1.5 text-sm font-medium {{ $category_id === '' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                الكل
            </button>
            @foreach ($categories as $category)
                <button type="button" wire:click="$set('category_id', '{{ $category->id }}')"
                    class="rounded-full px-4 py-1.5 text-sm font-medium {{ (string) $category_id === (string) $category->id ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                    {{ $category->name }} ({{ $category->articles_count }})
                </button>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($articles as $article)
            <a href="{{ route('articles.show', $article) }}" wire:key="article-{{ $article->id }}" class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                <div class="flex h-36 items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-4xl">
                    {{ $article->featured_image ? '' : '📰' }}
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-primary-600">{{ $article->category->name ?? 'عام' }}</span>
                    <h2 class="mt-1 font-bold text-gray-900 group-hover:text-primary-700">{{ $article->title }}</h2>
                    <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $article->excerpt ?: \Illuminate\Support\Str::limit($article->content, 100) }}</p>
                    <p class="mt-3 text-xs text-gray-400">
                        {{ $article->published_at?->translatedFormat('d M Y') ?? $article->created_at->translatedFormat('d M Y') }} ·
                        👁 {{ $article->views_count }}
                    </p>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-500">لا توجد مقالات بعد</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $articles->links() }}</div>
</div>
