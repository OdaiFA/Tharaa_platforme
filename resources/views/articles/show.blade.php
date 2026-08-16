@extends('layouts.app')

@section('title', $article->title)

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
            <span class="text-xs font-bold text-primary-600">{{ $article->category->name ?? 'عام' }}</span>
            <h1 class="mt-2 text-3xl font-extrabold leading-snug text-gray-900">{{ $article->title }}</h1>
            <p class="mt-3 flex items-center gap-3 text-xs text-gray-400">
                <span>{{ $article->author?->name ?? 'فريق ثراء' }}</span>
                <span>·</span>
                <span>{{ $article->published_at?->translatedFormat('d M Y') ?? $article->created_at->translatedFormat('d M Y') }}</span>
                <span>·</span>
                <span>{{ $article->views_count }} مشاهدة</span>
            </p>

            <div class="mt-6 border-t border-gray-100 pt-6 leading-loose text-gray-700">
                {!! \Illuminate\Support\Str::markdown($article->content) !!}
            </div>
        </div>

        <a href="{{ route('articles.index') }}" class="mt-6 inline-block text-sm font-bold text-primary-600 hover:underline">
            ← العودة إلى المقالات
        </a>
    </div>
@endsection
