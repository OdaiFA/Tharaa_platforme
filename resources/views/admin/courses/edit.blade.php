@extends('layouts.admin')

@section('title', 'تعديل دورة')

@section('content')
    <livewire:admin.courses.course-form :course-id="$course->id" />
@endsection
