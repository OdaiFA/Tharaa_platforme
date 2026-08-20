@extends('layouts.app')

@section('title', 'تعديل ميزانية')

@section('content')
    <livewire:budgets.budget-form :budget-id="$budget->id" />
@endsection
