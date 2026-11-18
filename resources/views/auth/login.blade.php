<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7fb">
    <title>Masuk · {{ config('app.name', 'CertifyPro') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f5f7fb] text-slate-800 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1fr_1fr]">
        <section class="relative hidden overflow-hidden bg-slate-950 px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between xl:px-20">
            <div class="absolute -right-24 top-20 size-96 rounded-full bg-indigo-600/30 blur-3xl"></div><div class="absolute -bottom-32 -left-28 size-96 rounded-full bg-violet-600/20 blur-3xl"></div>
            <a href="{{ route('dashboard') }}" class="relative flex items-center gap-3"><span class="flex size-10 items-center justify-center rounded-xl bg-indigo-500 text-white"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="text-lg font-bold tracking-tight">Certify<span class="text-indigo-300">Pro</span></span></a>
            <div class="relative max-w-lg py-16">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-indigo-100"><span class="size-1.5 rounded-full bg-emerald-300"></span> Prakerin, lebih terorganisir</span>
                <h1 class="mt-6 text-4xl font-bold leading-[1.15] tracking-tight xl:text-5xl">Setiap pengalaman,<br><span class="text-indigo-300">layak dirayakan.</span></h1>
                <p class="mt-5 max-w-md text-sm leading-7 text-slate-300">Kelola proses Praktik Kerja Industri dan hadirkan sertifikat yang merekam langkah penting peserta didik.</p>
                <div class="mt-10 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.07] p-4 backdrop-blur-sm"><span class="flex size-10 items-center justify-center rounded-xl bg-emerald-400/15 text-emerald-300"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="m5 12.5 4.2 4.2L19.5 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div><p class="text-xs font-semibold text-white">Satu platform untuk alur Prakerin</p><p class="mt-1 text-[10px] text-slate-300">Data siswa · Mitra industri · Sertifikat</p></div></div>
            </div>
            <p class="relative text-[10px] text-slate-400">© {{ date('Y') }} CertifyPro · Sistem Manajemen Prakerin</p>
            <div class="pointer-events-none absolute bottom-32 right-12 hidden rotate-[6deg] rounded-2xl border border-white/15 bg-white/[0.08] p-5 shadow-2xl backdrop-blur-sm xl:block">
                <div class="flex items-center gap-3"><span class="flex size-9 items-center justify-center rounded-lg bg-indigo-400/20 text-indigo-200"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span><span class="block text-[10px] font-semibold">Sertifikat Prakerin</span><span class="mt-1 block text-[9px] text-slate-300">Pengalaman yang membentuk masa depan</span></span></div><div class="mt-4 h-px w-52 bg-white/15"></div><div class="mt-3 flex items-center justify-between"><span class="text-[8px] uppercase tracking-[0.18em] text-slate-300">Kompetensi & pengalaman</span><span class="text-amber-300">✦</span></div>
            </div>
        </section>

        <section class="flex items-center justify-center px-5 py-12 sm:px-10">
            <div class="w-full max-w-[420px]">
                <a href="{{ route('dashboard') }}" class="mb-10 inline-flex items-center gap-2 text-xs font-medium text-slate-500 transition hover:text-indigo-600 lg:hidden"><span aria-hidden="true">←</span> Kembali ke dashboard</a>
                <div class="mb-8 lg:mb-10"><div class="mb-5 flex size-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 lg:hidden"><svg viewBox="0 0 24 24" fill="none" class="size-6" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Selamat datang kembali</p><h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">Masuk ke CertifyPro</h2><p class="mt-2 text-xs leading-6 text-slate-500">Gunakan akun sekolah untuk melanjutkan ke ruang kerja Prakerin.</p></div>
                <form class="grid gap-5" method="POST" action="{{ route('login.store') }}">
                    @csrf
                    @if ($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] leading-5 text-rose-700">{{ $errors->first() }}</div>
                    @endif
                    @if (session('success'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] leading-5 text-emerald-700">{{ session('success') }}</div>
                    @endif
                    @if (session('status'))
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-[11px] leading-5 text-indigo-700">{{ session('status') }}</div>
                    @endif
                    <label class="grid gap-2 text-xs font-semibold text-slate-700">Email admin<input type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="nama@sekolah.sch.id" class="h-12 rounded-xl border border-slate-200 bg-white px-4 text-sm font-normal outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50" required autofocus></label>
                    <label class="grid gap-2 text-xs font-semibold text-slate-700">Kata sandi<span class="relative"><input type="password" name="password" autocomplete="current-password" placeholder="Masukkan kata sandi" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 pr-12 text-sm font-normal outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50" required><button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-50 hover:text-slate-600" data-password-toggle aria-label="Tampilkan kata sandi"><svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true"><path d="M2.75 12s3.3-6 9.25-6 9.25 6 9.25 6-3.3 6-9.25 6-9.25-6-9.25-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg></button></span></label>
                    <div class="flex items-center justify-between"><label class="flex items-center gap-2 text-[11px] text-slate-500"><input type="checkbox" name="remember" value="1" class="size-3.5 rounded border-slate-300 accent-indigo-600"> Ingat saya</label><span class="text-[10px] text-slate-400">Hubungi admin sekolah untuk reset</span></div>
                    <button type="submit" class="mt-1 inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 text-xs font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:-translate-y-0.5 hover:bg-indigo-700">Masuk ke akun <span aria-hidden="true">→</span></button>
                </form>
                <div class="my-7 flex items-center gap-3"><span class="h-px flex-1 bg-slate-200"></span><span class="text-[9px] text-slate-400">RUANG KERJA SEKOLAH</span><span class="h-px flex-1 bg-slate-200"></span></div>
                <p class="text-center text-[10px] leading-5 text-slate-400">@if ($canSetup) Belum ada akun admin? <a href="{{ route('setup') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Siapkan aplikasi</a><br>@endif Butuh bantuan masuk? Hubungi administrator sekolah.<br><a href="{{ route('about') }}" class="mt-1 inline-flex font-semibold text-indigo-600 hover:text-indigo-800">Tentang CertifyPro</a></p>
                <p class="mt-10 text-center text-[9px] text-slate-400">© {{ date('Y') }} CertifyPro · Sistem Manajemen Prakerin</p>
            </div>
        </section>
    </main>
</body>
</html>
