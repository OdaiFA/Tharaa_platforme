@extends('layouts.app')

@section('title', 'تعديل معاملة')

@section('content')
    <livewire:transactions.transaction-form :transaction-id="$transaction->id" />
@endsection
