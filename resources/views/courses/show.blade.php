@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <livewire:courses.course-show :course="$course" />
@endsection
