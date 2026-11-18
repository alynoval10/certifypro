@if (session('success'))
    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800" role="status">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-800" role="alert">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-800" role="alert">{{ $errors->first() }}</div>
@endif
