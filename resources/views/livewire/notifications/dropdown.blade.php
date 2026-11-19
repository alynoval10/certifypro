<div class="relative" wire:poll.10m x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false">
    <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-label="Notifikasi" class="relative flex size-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
        <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @if ($notificationCount > 0)<span class="absolute -right-0.5 -top-0.5 flex min-w-4 items-center justify-center rounded-full border-2 border-white bg-rose-500 px-1 text-[9px] font-bold leading-3 text-white dark:border-slate-900">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>@endif
    </button>
    <div x-cloak x-show="open" x-transition.opacity class="absolute right-0 top-12 z-50 w-[min(90vw,360px)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900">
        <div class="border-b border-slate-100 px-4 py-3 dark:border-slate-800"><p class="text-xs font-bold text-slate-900 dark:text-white">Pengingat Prakerin</p><p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">{{ $academicYear ? 'Tahun ajaran '.$academicYear : 'Semua tahun ajaran' }}</p></div>
        <div class="max-h-80 overflow-y-auto p-2">
            @forelse ($notifications as $notification)
                <a wire:navigate href="{{ route($notification['route']) }}" @click="open = false" class="flex items-start gap-3 rounded-xl px-3 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                    <span @class(['mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold', 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' => $notification['tone'] === 'amber', 'bg-sky-50 text-sky-700 dark:bg-sky-950 dark:text-sky-300' => $notification['tone'] === 'sky', 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $notification['tone'] === 'emerald'])>{{ $notification['count'] }}</span>
                    <span class="min-w-0 flex-1"><span class="block text-xs font-semibold text-slate-800 dark:text-slate-100">{{ $notification['title'] }}</span><span class="mt-1 block text-[10px] leading-4 text-slate-500 dark:text-slate-400">{{ $notification['description'] }}</span></span>
                    <span class="mt-1 text-xs text-slate-400" aria-hidden="true">→</span>
                </a>
            @empty
                <div class="px-4 py-7 text-center"><span class="mx-auto flex size-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">✓</span><p class="mt-2 text-xs font-semibold text-slate-800 dark:text-slate-100">Tidak ada pengingat</p><p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">Semua proses utama sudah tertangani.</p></div>
            @endforelse
        </div>
    </div>
</div>
