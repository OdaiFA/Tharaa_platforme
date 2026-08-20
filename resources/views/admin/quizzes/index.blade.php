@extends('layouts.admin')

@section('title', 'اختبار الدرس: ' . $lesson->title)

@section('content')
    <livewire:admin.quizzes.quiz-form :lesson-id="$lesson->id" />
@endsection
