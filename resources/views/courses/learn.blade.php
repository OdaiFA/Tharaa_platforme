@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <livewire:learning.course-learn :course="$course" />
@endsection
