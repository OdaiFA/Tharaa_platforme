@extends('layouts.guest')

@section('title', 'إنشاء حساب')

@section('content')
    <div class="animate-fade-in mx-auto w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-xl shadow-primary-900/5">
        <h1 class="text-center text-2xl font-extrabold text-gray-900">إنشاء حساب جديد</h1>
        <p class="mt-1 text-center text-sm text-gray-500">ابدأ رحلتك نحو الثقافة المالية</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">الاسم الكامل</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="date_of_birth" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ الميلاد</label>
                <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required max="{{ now()->subYears(7)->format('Y-m-d') }}"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">كلمة المرور</label>
                <input id="password" type="password" name="password" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 transition hover:bg-primary-700">
                إنشاء الحساب
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:underline">تسجيل الدخول</a>
        </p>
    </div>
@endsection
