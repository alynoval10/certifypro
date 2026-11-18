<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7fb">
    <title>@isset($title){{ $title }}@else @yield('title', 'Dashboard')@endisset · {{ config('app.name', 'CertifyPro') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#f5f7fb] text-slate-800 antialiased">
    <div class="min-h-screen lg:flex">
        <div class="fixed inset-0 z-40 hidden bg-slate-950/40 backdrop-blur-[2px] lg:hidden" data-sidebar-overlay></div>
        <aside class="fixed inset-y-0 left-0 z-50 flex w-[264px] -translate-x-full flex-col border-r border-slate-200/80 bg-white transition-transform duration-300 lg:static lg:z-auto lg:translate-x-0" data-sidebar>
            <a href="{{ route('dashboard') }}" class="flex h-[84px] items-center gap-3 border-b border-slate-100 px-7" aria-label="CertifyPro, ke dashboard">
                <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20">
                    <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="m15.25 18.5 1.35 1.35 2.9-3.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span><span class="block text-[17px] font-bold tracking-tight text-slate-900">Certify<span class="text-indigo-600">Pro</span></span><span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Prakerin Management</span></span>
            </a>

            <div class="px-4 pt-7">
                <p class="px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Menu utama</p>
                <nav class="mt-3 grid gap-1.5" aria-label="Navigasi utama">
                    <a wire:navigate href="{{ route('dashboard') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('dashboard')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.6" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="4" rx="1.6" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="10.5" width="7" height="10" rx="1.6" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.6" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Ringkasan</span>
                    </a>
                    <a wire:navigate href="{{ route('certificates.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('certificates.*') && ! request()->routeIs('certificates.verify')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span class="flex-1">Sertifikat</span>
                    </a>
                    <a wire:navigate href="{{ route('students.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('students.*')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><circle cx="9" cy="8" r="3.25" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19.5v-1.25A4.25 4.25 0 0 1 7.75 14h2.5a4.25 4.25 0 0 1 4.25 4.25v1.25M15.5 5.15a3.25 3.25 0 0 1 0 5.7M17 14.2a4.25 4.25 0 0 1 3.5 4.18v1.12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span class="flex-1">Peserta didik</span>
                    </a>
                    <a wire:navigate href="{{ route('companies.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('companies.*')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><path d="M3.5 20.25h17M5.5 20V7.5l6.5-3.75 6.5 3.75V20M9 20v-5.5h6V20M8.25 9h.01M12 9h.01M15.75 9h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="flex-1">Mitra industri</span>
                    </a>
                    <a wire:navigate href="{{ route('internships.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('internships.*')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><rect x="3.5" y="5.5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 5.5V4.25A1.75 1.75 0 0 1 9.75 2.5h4.5A1.75 1.75 0 0 1 16 4.25V5.5M3.5 11h17M9 10.5v1M15 10.5v1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span class="flex-1">Data Prakerin</span>
                    </a>
                    <a wire:navigate href="{{ route('school-structure.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('school-structure.*')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><path d="M4 20V5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5V20M8 8h3M8 12h3M14 8h2M14 12h2M3 20h18M9 20v-4h6v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="flex-1">Data akademik</span>
                    </a>
                    <a wire:navigate href="{{ route('assessments.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('assessments.*')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><path d="M5 3.75h14a1.5 1.5 0 0 1 1.5 1.5v14.5H3.5V5.25A1.5 1.5 0 0 1 5 3.75Z" stroke="currentColor" stroke-width="1.7"/><path d="M7.5 8h9M7.5 12h4M7.5 16h4m4.5-4.5 1.2 1.2 2.3-2.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="flex-1">Penilaian siswa</span>
                    </a>
                </nav>
            </div>

            <div class="mt-8 px-4">
                <p class="px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Lainnya</p>
                <nav class="mt-3 grid gap-1.5" aria-label="Navigasi lainnya">
                    <a wire:navigate href="{{ route('about') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('about')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 11v5M12 8h.01" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                        <span>Tentang aplikasi</span>
                    </a>
                    <a wire:navigate href="{{ route('settings.edit') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('settings.*')])>
                        <svg viewBox="0 0 24 24" fill="none" class="size-[18px]" aria-hidden="true"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/><path d="m19.4 15 .1.1a1.7 1.7 0 0 1-2.4 2.4l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a1.7 1.7 0 0 1-3.4 0v-.2a1.7 1.7 0 0 0-2.9-1.2l-.1.1a1.7 1.7 0 1 1-2.4-2.4l.1-.1a1.7 1.7 0 0 0-1.2-2.9H4a1.7 1.7 0 0 1 0-3.4h.2A1.7 1.7 0 0 0 5.4 5.8l-.1-.1a1.7 1.7 0 1 1 2.4-2.4l.1.1a1.7 1.7 0 0 0 2.9-1.2V2a1.7 1.7 0 0 1 3.4 0v.2A1.7 1.7 0 0 0 17 3.4l.1-.1a1.7 1.7 0 1 1 2.4 2.4l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.2a1.7 1.7 0 0 1 0 3.4h-.2a1.7 1.7 0 0 0-1.2 2.9Z" stroke="currentColor" stroke-width="1.35" stroke-linejoin="round"/></svg>
                        <span>Pengaturan sekolah</span>
                    </a>
                </nav>
            </div>

            <div class="mt-auto p-4">
                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-violet-50 p-4">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm">
                        <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true"><path d="m12 3 1.55 5.45L19 10l-5.45 1.55L12 17l-1.55-5.45L5 10l5.45-1.55L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z" fill="currentColor"/></svg>
                    </div>
                    <p class="mt-3 text-xs font-bold text-slate-800">Ruang berkembang</p>
                    <p class="mt-1 text-[11px] leading-5 text-slate-500">Kelola pengalaman Prakerin dengan lebih rapi dan terukur.</p>
                    <a href="{{ route('about') }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600">Kenali CertifyPro <span aria-hidden="true">→</span></a>
                </div>
                <div class="mt-4 flex items-center gap-2 px-2 text-[10px] text-slate-400"><span class="size-1.5 rounded-full bg-emerald-500"></span> Sistem siap digunakan <span class="ml-auto">v1.0</span></div>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-30 flex h-[72px] items-center justify-between border-b border-slate-200/70 bg-white/90 px-5 backdrop-blur-xl sm:px-8 lg:px-10">
                <div class="flex items-center gap-3">
                    <button type="button" class="flex size-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 lg:hidden" data-sidebar-toggle aria-label="Buka menu">
                        <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M4 6.5h16M4 12h16M4 17.5h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <div class="hidden items-center gap-2 text-xs text-slate-400 sm:flex"><span>CertifyPro</span><span>/</span><span class="font-semibold text-slate-700">{{ $breadcrumb ?? $__env->yieldContent('breadcrumb', 'Ringkasan') }}</span></div>
                    <span class="text-sm font-semibold text-slate-800 sm:hidden">{{ $breadcrumb ?? $__env->yieldContent('breadcrumb', 'Ringkasan') }}</span>
                </div>
                <div class="flex items-center gap-3 sm:gap-5">
                    <div class="relative hidden w-56 xl:block">
                        <svg viewBox="0 0 24 24" fill="none" class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4.25 4.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <input type="search" placeholder="Cari sesuatu..." class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                    </div>
                    <button type="button" class="relative flex size-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100" aria-label="Notifikasi">
                        <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg><span class="absolute right-2 top-2 size-2 rounded-full border-2 border-white bg-rose-500"></span>
                    </button>
                    <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
                    <button type="button" class="flex items-center gap-2.5 text-left">
                        <span class="flex size-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-xs font-bold text-white ring-2 ring-indigo-100">{{ auth()->user() ? mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) : 'CP' }}</span>
                        <span class="hidden sm:block"><span class="block text-xs font-semibold text-slate-800">{{ auth()->user()?->name ?? 'Pengunjung' }}</span><span class="mt-0.5 block text-[10px] text-slate-400">{{ auth()->check() ? 'Administrator' : 'CertifyPro' }}</span></span>
                    </button>
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="block">@csrf<button type="submit" class="rounded-lg px-2 py-1.5 text-[10px] font-semibold text-slate-500 hover:bg-slate-100 hover:text-rose-600">Keluar</button></form>
                    @else
                        <a href="{{ route('login') }}" class="hidden rounded-lg px-2 py-1.5 text-[10px] font-semibold text-indigo-600 hover:bg-indigo-50 sm:block">Masuk</a>
                    @endauth
                </div>
            </header>
            <main class="mx-auto w-full max-w-[1600px] px-5 py-7 sm:px-8 sm:py-9 lg:px-10">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
                <footer class="mt-10 flex flex-col gap-2 border-t border-slate-200/80 pt-5 text-[11px] text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                    <span>© {{ date('Y') }} CertifyPro · Sistem Manajemen Prakerin</span>
                    <div class="flex gap-4"><a href="{{ route('about') }}" class="font-medium transition hover:text-indigo-600">Tentang aplikasi</a><a href="{{ route('settings.edit') }}" class="font-medium transition hover:text-indigo-600">Pengaturan</a></div>
                </footer>
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
