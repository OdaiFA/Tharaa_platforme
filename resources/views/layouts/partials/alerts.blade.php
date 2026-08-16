@if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4">
        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="mb-4 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
        <span>{{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800">&times;</button>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <span>{{ session('error') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800">&times;</button>
    </div>
@endif

@if (session('info'))
    <div class="mb-4 flex items-center justify-between rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
        <span>{{ session('info') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-800">&times;</button>
    </div>
@endif
