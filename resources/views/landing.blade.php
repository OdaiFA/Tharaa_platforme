@extends('layouts.guest')

@section('title', 'منصة تعليمية شاملة للثقافة المالية')

@section('content')
    <section class="relative overflow-hidden rounded-3xl bg-hero-gradient px-5 py-12 text-white sm:px-10 sm:py-16">
        <div class="pointer-events-none absolute -top-16 -left-16 h-48 w-48 rounded-full bg-gold-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -right-10 h-56 w-56 rounded-full bg-primary-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute top-8 left-6 hidden animate-float lg:block">
            <span class="text-4xl drop-shadow-lg">💸</span>
        </div>

        <div class="relative grid items-center gap-10 lg:grid-cols-2">
            <div class="text-center lg:text-right">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-sm font-medium text-gold-300 backdrop-blur">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zm7-10a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/></svg>
                    تعلّم، وادخّر، ونمّ ثروتك بذكاء
                </div>

                <h1 class="font-display text-4xl font-black leading-[1.3] md:text-5xl md:leading-[1.3]">
                    منصة
                    <span class="bg-gold-gradient bg-clip-text text-transparent">ثراء</span>
                    <br>للثقافة المالية
                </h1>

                <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-slate-300 md:text-lg lg:mx-0">
                    إدارة مالية شخصية، وميزانيات ذكية، ودورات تعليمية مخصصة حسب الفئة العمرية،
                    ومقالات تثقيفية وتحديات يومية — كل ذلك في مكان واحد.
                </p>

                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row lg:justify-start">
                    <a href="{{ route('register') }}" class="btn-gold w-full px-8 py-3 text-base sm:w-auto">
                        ابدأ مجاناً الآن
                    </a>
                    <a href="{{ route('login') }}" class="w-full rounded-xl border-2 border-white/25 bg-white/5 px-8 py-3 text-base font-bold text-white backdrop-blur transition hover:bg-white/15 sm:w-auto">
                        تسجيل الدخول
                    </a>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs text-slate-300 lg:justify-start">
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        مجاناً بالكامل
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        بياناتك آمنة ومشفّرة
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zm7-10a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/></svg>
                        تعلم بالعربية بالكامل
                    </span>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-white">ملخصك المالي</span>
                        <span class="chip border border-white/15 bg-white/10 text-gold-300">شهري</span>
                    </div>

                    <div class="mt-4 rounded-2xl bg-financial-card p-5">
                        <p class="text-xs text-slate-400">إجمالي الرصيد</p>
                        <p class="font-num mt-1 text-2xl font-bold text-white">١٥٬٢٤٠ <span class="text-xs font-medium text-slate-400">ر.س</span></p>
                        <div class="mt-3 flex flex-wrap items-center gap-4 text-xs">
                            <span class="font-num flex items-center gap-1 text-green-400">▲ ٤٬٢٠٠ دخول</span>
                            <span class="font-num flex items-center gap-1 text-red-400">▼ ١٬٨٥٠ مصاريف</span>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-200">هدف الادخار</span>
                                <span class="font-num font-bold text-gold-400">٦٨٪</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-white/15">
                                <div class="h-full w-[68%] rounded-full bg-gold-gradient"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-200">دورة الاستثمار الذكي</span>
                                <span class="font-num font-bold text-green-400">٤٥٪</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-white/15">
                                <div class="h-full w-[45%] rounded-full bg-brand-gradient"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-gold-300">🏅 إنجاز جديد</span>
                        <span class="font-num inline-flex items-center gap-1 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-green-300">⚡ +٢٠ نقطة خبرة</span>
                    </div>
                </div>

                <div class="pointer-events-none absolute -top-5 -right-5 hidden animate-float xl:block">
                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-gold-gradient px-4 py-2 text-sm font-bold text-navy-900 shadow-lg">
                        🏆 ٨ شارات
                    </span>
                </div>
                <div class="pointer-events-none absolute -bottom-5 -left-5 hidden animate-float xl:block" style="animation-delay: 1.2s">
                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-brand-gradient px-4 py-2 text-sm font-bold text-white shadow-lg">
                        🔒 تشفير كامل
                    </span>
                </div>
            </div>
        </div>

        <div class="relative mt-12 grid grid-cols-2 gap-4 border-t border-white/10 pt-8 sm:grid-cols-4">
            <div>
                <p class="font-num text-2xl font-bold text-gold-400 md:text-3xl">+٥٠</p>
                <p class="mt-1 text-xs text-slate-400 md:text-sm">درساً تعليمياً</p>
            </div>
            <div>
                <p class="font-num text-2xl font-bold text-gold-400 md:text-3xl">٥</p>
                <p class="mt-1 text-xs text-slate-400 md:text-sm">مسارات تعلّم</p>
            </div>
            <div>
                <p class="font-num text-2xl font-bold text-gold-400 md:text-3xl">+١٠٠٠</p>
                <p class="mt-1 text-xs text-slate-400 md:text-sm">متعلّم نشط</p>
            </div>
            <div>
                <p class="font-num text-2xl font-bold text-gold-400 md:text-3xl">+١٠</p>
                <p class="mt-1 text-xs text-slate-400 md:text-sm">مقالاً تثقيفياً</p>
            </div>
        </div>
    </section>

    <section class="mt-12">
        <div class="text-center">
            <span class="chip border border-gold-200 bg-gold-50 text-gold-700">ماذا تقدم ثراء؟</span>
            <h2 class="mt-3 text-2xl font-extrabold text-navy-800 md:text-3xl">كل ما تحتاجه لمالية أفضل</h2>
            <p class="mx-auto mt-2 max-w-xl text-sm leading-relaxed text-gray-500 md:text-base">
                أدوات مالية ذكية، ومحتوى تعليمي ممتع، وتحفيز يومي — منصة واحدة تجمع بين الفائدة والمتعة.
            </p>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="card p-6 transition-shadow hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-gradient text-white shadow-md shadow-primary-600/30">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="font-bold text-navy-800">تتبع المصاريف والمداخيل</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">سجّل كل معاملة مالية عبر حسابات متعددة مع تصنيفات واضحة، ورصد فوري للأرصدة.</p>
            </div>

            <div class="card p-6 transition-shadow hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-gradient text-white shadow-md shadow-primary-600/30">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="font-bold text-navy-800">ميزانيات ذكية</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">ضع حدوداً للإنفاق الشهري في كل تصنيف واحصل على تنبيهات فورية عند الاقتراب من الحد.</p>
            </div>

            <div class="card p-6 transition-shadow hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gold-gradient text-navy-900 shadow-md shadow-gold-500/40">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-navy-800">أهداف ادخار</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">حدد أهدافك المالية وتابع التقدم نحوها مع إسهامات دورية وتذكيرات ذكية.</p>
            </div>

            <div class="card p-6 transition-shadow hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-navy-800 text-white shadow-md shadow-navy-800/30">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="font-bold text-navy-800">دورات ومقالات تعليمية</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">محتوى تعليمي مصمم حسب الفئة العمرية، مع شهادات إتمام للدورات وتقييم مستوى.</p>
            </div>

            <div class="card p-6 transition-shadow hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-purple-600 text-white shadow-md shadow-purple-600/30">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <h3 class="font-bold text-navy-800">تحفيز وتحديات يومية</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">نقاط خبرة وإنجازات وشارات تحافظ على دافعك، مع تحديات يومية تجعل التعلم عادة.</p>
            </div>

            <div class="card p-6 transition-shadow hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white shadow-md shadow-blue-600/30">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l3-9h12l3 9M3 11l3 9m-3-9h18m0 0l-3 9M7 20h10m-7-9l3-3 3 3m-6 0l-2 2m2-2l2-2 2 2"/></svg>
                </div>
                <h3 class="font-bold text-navy-800">أدوات حساب ذكية</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">حاسبات الادخار والميزانية تساعدك على اتخاذ قرارات مالية أفضل بسهولة.</p>
            </div>
        </div>
    </section>

    <section class="mt-12 grid grid-cols-1 items-center gap-6 lg:grid-cols-2">
        <div class="rounded-3xl bg-financial-card p-7 text-white sm:p-9">
            <span class="chip border border-white/15 bg-white/10 text-gold-300">مسارات التعلم</span>
            <h2 class="mt-3 text-2xl font-extrabold md:text-3xl">ابدأ رحلتك نحو <span class="text-gold-400">الاستقلال المالي</span></h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-300 md:text-base">
                مسارات منظمة حسب مستواك وعمرك: من أساسيات الادخار إلى الاستثمار الذكي،
                مع دروس تفاعلية واختبارات وشهادات إتمام.
            </p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('courses.index') }}" class="btn-gold w-full sm:w-auto">استكشف الدورات</a>
                <a href="{{ route('register') }}" class="w-full rounded-xl border-2 border-white/25 px-6 py-2.5 text-center text-sm font-bold text-white transition hover:bg-white/10 sm:w-auto">
                    أنشئ حسابك
                </a>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-700">✅</span>
                <div>
                    <h3 class="font-bold text-navy-800">درس مكتمل</h3>
                    <p class="mt-0.5 text-sm text-gray-500">وسام أخضر عند إتمام كل درس بنجاح</p>
                </div>
            </div>
            <div class="card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gold-100 text-gold-700">🏅</span>
                <div>
                    <h3 class="font-bold text-navy-800">شارة ذهبية</h3>
                    <p class="mt-0.5 text-sm text-gray-500">لإنجازاتك المالية والتعليمية الكبرى</p>
                </div>
            </div>
            <div class="card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-100 text-purple-700">👑</span>
                <div>
                    <h3 class="font-bold text-navy-800">شارة بلاتينية نادرة</h3>
                    <p class="mt-0.5 text-sm text-gray-500">للمتميزين والمتفوقين في المسارات</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-12 overflow-hidden rounded-3xl bg-gold-gradient px-5 py-10 text-center text-navy-900 sm:px-10">
        <h2 class="text-2xl font-extrabold md:text-3xl">جاهز تبدأ رحلتك المالية؟</h2>
        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed opacity-80 md:text-base">
            انضم إلى آلاف المتعلمين وابدأ بتثقيف نفسك مالياً اليوم — مجاناً.
        </p>
        <a href="{{ route('register') }}" class="mt-6 inline-block rounded-xl bg-navy-900 px-8 py-3 text-base font-bold text-white shadow-lg transition hover:bg-navy-950">
            أنشئ حسابك المجاني
        </a>
    </section>
@endsection
