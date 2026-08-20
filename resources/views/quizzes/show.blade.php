@extends('layouts.app')

@section('title', 'اختبار: ' . $quiz->title)

@section('content')
    <livewire:quizzes.quiz-show :quiz="$quiz" />
@endsection
