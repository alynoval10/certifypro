<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7fb">
    <title>Penyiapan awal · {{ config('app.name', 'CertifyPro') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f5f7fb] px-4 py-10 text-slate-800 sm:px-6">
    <main class="mx-auto max-w-2xl">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3"><span class="flex size-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="text-lg font-bold tracking-tight text-slate-900">Certify<span class="text-indigo-600">Pro</span></span></a>
        <section class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl shadow-slate-200/40 sm:p-9">
            <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-indigo-700"><span class="size-1.5 rounded-full bg-indigo-500"></span> Penyiapan awal · hanya sekali</span>
            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">Siapkan akun admin dan identitas sekolah</h1>
            <p class="mt-2 text-xs leading-6 text-slate-500">Akun admin pertama mengamankan aplikasi. Setelah dibuat, halaman penyiapan ini otomatis dinonaktifkan.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] leading-5 text-rose-700">Periksa kembali isian: {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('setup.store') }}" class="mt-7 grid gap-6">
                @csrf
                <div><h2 class="text-xs font-bold text-slate-800">Akun administrator</h2><div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="field-label">Nama admin<input class="field-input" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="120"></label>
                    <label class="field-label">Email login<input class="field-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
                    <label class="field-label">Kata sandi (minimal 10 karakter)<input class="field-input" type="password" name="password" autocomplete="new-password" required minlength="10"></label>
                    <label class="field-label">Ulangi kata sandi<input class="field-input" type="password" name="password_confirmation" autocomplete="new-password" required minlength="10"></label>
                </div></div>
                <div class="h-px bg-slate-100"></div>
                <div><h2 class="text-xs font-bold text-slate-800">Identitas sekolah</h2><div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="field-label sm:col-span-2">Nama sekolah<input class="field-input" name="school_name" value="{{ old('school_name') }}" required maxlength="255" placeholder="Contoh: SMK Negeri 1 ..."></label>
                    <label class="field-label">NPSN (opsional)<input class="field-input" name="npsn" value="{{ old('npsn') }}" maxlength="20"></label>
                    <label class="field-label">Nama kepala sekolah<input class="field-input" name="principal_name" value="{{ old('principal_name') }}" required maxlength="120"></label>
                </div></div>
                <button type="submit" class="mt-1 inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">Buat akun dan mulai <span aria-hidden="true">→</span></button>
                <p class="text-center text-[10px] leading-5 text-slate-400">Kata sandi disimpan dalam bentuk hash. Halaman ini hanya berfungsi sebelum akun admin pertama dibuat.</p>
            </form>
        </section>
    </main>
</body>
</html>
