@extends('layouts.app')

@section('title', 'تعديل حساب')

@section('content')
    <livewire:accounts.account-form :account-id="$account->id" />
@endsection
