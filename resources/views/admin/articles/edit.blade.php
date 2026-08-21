@extends('layouts.admin')

@section('title', 'تعديل مقال')

@section('content')
    <livewire:admin.articles.article-form :article-id="$article->id" />
@endsection
