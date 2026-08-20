@extends('layouts.app')

@section('title', $budget->name)

@section('content')
    <livewire:budgets.budget-show :budget="$budget" />
@endsection
