<footer class="mt-16 bg-hero-gradient text-white">
    <div class="mx-auto max-w-7xl px-4 pt-12 pb-24 sm:pb-12">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-4">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-gradient text-lg font-extrabold text-white shadow-lg shadow-primary-600/30">ثر</span>
                    <span class="text-xl font-extrabold">ثراء</span>
                </a>
                <p class="max-w-xs text-sm leading-relaxed text-slate-400">
                    منصة عربية متكاملة للثقافة المالية والتعليم التفاعلي — أدوات إدارة مالية ذكية،
                    ودورات تعليمية، وتحديات تحفيزية تناسب جميع الفئات العمرية.
                </p>
                <div class="flex items-center gap-2">
                    <a href="#" aria-label="تويتر" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-slate-300 transition hover:bg-white/20 hover:text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" aria-label="انستغرام" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-slate-300 transition hover:bg-white/20 hover:text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" aria-label="لينكدإن" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-slate-300 transition hover:bg-white/20 hover:text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.225 0z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gold-400">المنصة</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('landing') }}" class="text-slate-300 transition hover:text-gold-400">الرئيسية</a></li>
                    <li><a href="{{ route('register') }}" class="text-slate-300 transition hover:text-gold-400">إنشاء حساب مجاني</a></li>
                    <li><a href="{{ route('login') }}" class="text-slate-300 transition hover:text-gold-400">تسجيل الدخول</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-slate-300 transition hover:text-gold-400">لوحة التحكم</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gold-400">التعلم المالي</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('courses.index') }}" class="text-slate-300 transition hover:text-gold-400">الدورات التدريبية</a></li>
                    <li><a href="{{ route('courses.recommended') }}" class="text-slate-300 transition hover:text-gold-400">الدورات الموصى بها</a></li>
                    <li><a href="{{ route('articles.index') }}" class="text-slate-300 transition hover:text-gold-400">المقالات التثقيفية</a></li>
                    @auth
                        <li><a href="{{ route('goals.index') }}" class="text-slate-300 transition hover:text-gold-400">أهداف الادخار</a></li>
                        <li><a href="{{ route('budgets.index') }}" class="text-slate-300 transition hover:text-gold-400">الميزانيات</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gold-400">تواصل معنا</h3>
                <ul class="mt-4 space-y-3 text-sm text-slate-300">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span dir="ltr">info@tharaa.sa</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>المملكة العربية السعودية</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span dir="ltr">+966 5X XXX XXXX</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-xs text-slate-400 sm:flex-row">
            <p>&copy; {{ date('Y') }} منصة ثراء للثقافة المالية — جميع الحقوق محفوظة</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('landing') }}" class="transition hover:text-gold-400">الرئيسية</a>
                <a href="{{ route('courses.index') }}" class="transition hover:text-gold-400">الدورات</a>
                <a href="{{ route('login') }}" class="transition hover:text-gold-400">الدخول</a>
            </div>
        </div>
    </div>
</footer>
