@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">الإشعارات</h1>
            <p class="mt-1 text-sm text-gray-500">آخر التنبيهات من المنصة</p>
        </div>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                تعليم الكل كمقروء
            </button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <div class="flex items-center justify-between rounded-2xl border {{ $notification->is_read ? 'border-gray-100 bg-white' : 'border-primary-200 bg-primary-50/40' }} p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    @if (! $notification->is_read)
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-primary-500"></span>
                    @endif
                    <div>
                        <p class="font-bold text-gray-900">{{ $notification->title }}</p>
                        <p class="mt-0.5 text-sm text-gray-600">{{ $notification->message }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if (! $notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-primary-700">
                            فتح
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-400">لا توجد إشعارات حالياً</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
@endsection
