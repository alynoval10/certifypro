@extends('layouts.app')

@section('title', 'Tentang Aplikasi')
@section('breadcrumb', 'Tentang Aplikasi')

@section('content')
    <div class="mx-auto max-w-5xl">
        <section class="relative overflow-hidden rounded-[28px] bg-slate-950 px-6 py-10 text-white sm:px-10 sm:py-14">
            <div class="absolute -right-20 -top-28 size-80 rounded-full bg-indigo-500/30 blur-3xl"></div>
            <div class="absolute -bottom-40 right-1/3 size-72 rounded-full bg-violet-500/20 blur-3xl"></div>
            <div class="relative max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-indigo-100"><span class="size-1.5 rounded-full bg-emerald-300"></span> Tentang CertifyPro</span>
                <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Prakerin lebih tertata,<br><span class="text-indigo-300">prestasi lebih bermakna.</span></h1>
                <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300">CertifyPro adalah ruang kerja digital untuk membantu sekolah mengelola perjalanan Praktik Kerja Industri—dari pendataan peserta hingga penerbitan sertifikat.</p>
            </div>
            <div class="relative mt-9 flex flex-wrap gap-3">
                <div class="rounded-xl border border-white/10 bg-white/[0.07] px-4 py-3"><p class="text-lg font-bold">Terpusat</p><p class="mt-1 text-[10px] text-slate-300">Data lebih mudah dikelola</p></div>
                <div class="rounded-xl border border-white/10 bg-white/[0.07] px-4 py-3"><p class="text-lg font-bold">Terpantau</p><p class="mt-1 text-[10px] text-slate-300">Progres terlihat jelas</p></div>
                <div class="rounded-xl border border-white/10 bg-white/[0.07] px-4 py-3"><p class="text-lg font-bold">Terpercaya</p><p class="mt-1 text-[10px] text-slate-300">Dokumen tersusun rapi</p></div>
            </div>
            <div class="pointer-events-none absolute -bottom-8 -right-4 hidden rotate-[-8deg] rounded-2xl border border-white/15 bg-white/[0.08] p-4 shadow-2xl backdrop-blur-sm sm:block lg:right-12 lg:bottom-8">
                <div class="flex items-center gap-3"><span class="flex size-9 items-center justify-center rounded-lg bg-indigo-400/20 text-indigo-200"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span><span class="block text-[10px] font-semibold text-white">Sertifikat Prakerin</span><span class="mt-0.5 block text-[9px] text-slate-300">Siap menjadi kenangan berharga</span></span></div>
                <div class="mt-4 h-px w-56 bg-white/15"></div><div class="mt-3 flex items-center justify-between"><span class="text-[8px] uppercase tracking-[0.18em] text-slate-300">Selesai dengan baik</span><span class="size-6 rounded-full border border-emerald-300/60 bg-emerald-300/15"></span></div>
            </div>
        </section>

        <section class="mt-7 grid gap-4 md:grid-cols-3">
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/30"><span class="about-icon bg-indigo-50 text-indigo-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 7.5v4.7l3.1 1.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h2 class="mt-4 text-sm font-bold text-slate-900">Hemat waktu</h2><p class="mt-2 text-xs leading-6 text-slate-500">Kurangi pencatatan berulang. Informasi peserta, mitra, dan dokumen tersimpan dalam alur yang mudah ditelusuri.</p></article>
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/30"><span class="about-icon bg-emerald-50 text-emerald-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M4 19.5V5.75A1.75 1.75 0 0 1 5.75 4h12.5A1.75 1.75 0 0 1 20 5.75V19.5H4Z" stroke="currentColor" stroke-width="1.7"/><path d="M8 8h8M8 11.5h8M8 15h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h2 class="mt-4 text-sm font-bold text-slate-900">Proses jelas</h2><p class="mt-2 text-xs leading-6 text-slate-500">Status setiap sertifikat membantu tim sekolah melihat dokumen yang masih perlu diperiksa maupun yang sudah diterbitkan.</p></article>
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/30"><span class="about-icon bg-amber-50 text-amber-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M12 3.5 19 6v5.4c0 4.3-2.9 7.6-7 9.1-4.1-1.5-7-4.8-7-9.1V6l7-2.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m8.7 12 2.1 2.1 4.5-4.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h2 class="mt-4 text-sm font-bold text-slate-900">Data lebih rapi</h2><p class="mt-2 text-xs leading-6 text-slate-500">Informasi kegiatan Prakerin dapat dikelola secara terpusat agar lebih konsisten dan siap ditinjau kapan saja.</p></article>
        </section>

        <section class="mt-7 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-200/30 sm:p-8">
            <div class="grid gap-8 md:grid-cols-[1.2fr_0.8fr] md:items-center">
                <div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Dibangun untuk sekolah</p><h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900">Mendukung kolaborasi pendidikan dan dunia kerja.</h2><p class="mt-3 text-xs leading-6 text-slate-500">Praktik Kerja Industri memberikan pengalaman nyata bagi peserta didik. CertifyPro membantu sekolah merawat proses administrasinya agar perhatian dapat lebih banyak tertuju pada pembelajaran dan perkembangan siswa.</p></div>
                <div class="rounded-xl bg-slate-50 p-5"><p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Informasi aplikasi</p><dl class="mt-4 grid gap-3 text-xs"><div class="flex justify-between gap-3"><dt class="text-slate-500">Nama</dt><dd class="font-semibold text-slate-800">CertifyPro</dd></div><div class="flex justify-between gap-3"><dt class="text-slate-500">Fokus</dt><dd class="font-semibold text-slate-800">Manajemen Prakerin</dd></div><div class="flex justify-between gap-3"><dt class="text-slate-500">Versi</dt><dd class="font-semibold text-slate-800">1.0.0</dd></div><div class="flex justify-between gap-3"><dt class="text-slate-500">Status</dt><dd class="inline-flex items-center gap-1.5 font-semibold text-emerald-700"><span class="size-1.5 rounded-full bg-emerald-500"></span>Pengembangan</dd></div></dl></div>
            </div>
        </section>
    </div>
@endsection
