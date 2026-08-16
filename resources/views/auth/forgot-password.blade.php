@extends('layouts.guest')

@section('title', 'استعادة كلمة المرور')

@section('content')
    <div class="animate-fade-in mx-auto w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-xl shadow-primary-900/5">
        <h1 class="text-center text-2xl font-extrabold text-gray-900">استعادة كلمة المرور</h1>
        <p class="mt-1 text-center text-sm text-gray-500">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين</p>

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 transition hover:bg-primary-700">
                إرسال رابط إعادة التعيين
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            تذكرت كلمة المرور؟
            <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:underline">تسجيل الدخول</a>
        </p>
    </div>
@endsection
