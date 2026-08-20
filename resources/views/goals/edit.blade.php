@extends('layouts.app')

@section('title', 'تعديل هدف')

@section('content')
    <livewire:goals.goal-form :goal-id="$goal->id" />
@endsection
