@extends('layouts.app')

@section('title', $article->title)

@section('content')
    <livewire:articles.article-show :article="$article" />
@endsection
