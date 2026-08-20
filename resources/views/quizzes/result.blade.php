@extends('layouts.app')

@section('title', 'نتيجة الاختبار')

@section('content')
    <livewire:quizzes.quiz-result :quiz="$quiz" :attempt="$attempt" />
@endsection
