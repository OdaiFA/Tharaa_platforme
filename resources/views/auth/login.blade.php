@extends('layouts.guest')

@section('title', 'تسجيل الدخول')

@section('content')
    <div class="animate-fade-in mx-auto w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-xl shadow-primary-900/5">
        <h1 class="text-center text-2xl font-extrabold text-gray-900">تسجيل الدخول</h1>
        <p class="mt-1 text-center text-sm text-gray-500">مرحباً بعودتك! أدخل بياناتك للمتابعة</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">كلمة المرور</label>
                <input id="password" type="password" name="password" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    تذكرني
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-600 hover:underline">نسيت كلمة المرور؟</a>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 transition hover:bg-primary-700">
                دخول
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            ليس لديك حساب؟
            <a href="{{ route('register') }}" class="font-bold text-primary-600 hover:underline">أنشئ حساباً مجانياً</a>
        </p>
    </div>
@endsection
