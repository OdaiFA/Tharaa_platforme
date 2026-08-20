@extends('layouts.admin')

@section('title', 'وحدات: ' . $course->title)

@section('content')
    <livewire:admin.modules.modules-index :course-id="$course->id" />
@endsection
