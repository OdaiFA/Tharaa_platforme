@extends('layouts.admin')

@section('title', 'أسئلة الاختبار: ' . $quiz->title)

@section('content')
    <livewire:admin.questions.questions-index :quiz-id="$quiz->id" />
@endsection
