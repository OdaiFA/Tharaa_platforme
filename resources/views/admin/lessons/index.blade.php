@extends('layouts.admin')

@section('title', 'دروس: ' . $module->title)

@section('content')
    <livewire:admin.lessons.lessons-index :module-id="$module->id" />
@endsection
