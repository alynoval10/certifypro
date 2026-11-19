<div>
    @include('partials.flash')

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-indigo-600">Perlindungan data</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Backup dan pemulihan</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Buat salinan database sekarang, unduh untuk disimpan di tempat aman, atau pulihkan database dari backup yang tersedia.</p>
        </div>
        <button type="button" wire:click="createBackup" wire:loading.attr="disabled" class="primary-button disabled:opacity-60">
            <span wire:loading.remove wire:target="createBackup">Buat backup sekarang</span>
            <span wire:loading wire:target="createBackup">Membuat backup...</span>
        </button>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <article class="form-card dark:border-slate-700 dark:bg-slate-900 lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div><h2 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat backup</h2><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ count($backups) }} file tersimpan di server ini.</p></div>
                <span class="rounded-full bg-indigo-50 px-3 py-1.5 text-[10px] font-bold uppercase text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ $databaseDriver }}</span>
            </div>
            <div class="mt-5 overflow-x-auto">
                <table class="data-table min-w-[680px]">
                    <thead><tr><th>Nama file</th><th>Waktu</th><th>Ukuran</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($backups as $backup)
                            <tr wire:key="backup-{{ $backup['name'] }}">
                                <td class="font-semibold text-slate-800 dark:text-slate-200">{{ $backup['name'] }}</td>
                                <td>{{ date('d M Y, H:i', $backup['modified']) }}</td>
                                <td>{{ number_format($backup['size'] / 1024, 1) }} KB</td>
                                <td><div class="flex justify-end gap-2">
                                    <button type="button" wire:click="downloadBackup('{{ $backup['name'] }}')" class="table-action">Unduh</button>
                                    <button type="button" wire:click="restoreBackup('{{ $backup['name'] }}')" wire:confirm="Database saat ini akan diganti dengan backup ini. Sistem akan membuat backup pengaman database sekarang terlebih dahulu. Lanjutkan?" class="table-action text-rose-600 hover:bg-rose-50">Pulihkan</button>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-10 text-center text-slate-500 dark:text-slate-400">Belum ada backup. Buat backup pertama untuk menyimpan salinan database.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="grid content-start gap-4">
            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/70 dark:bg-amber-950/30">
                <h2 class="text-sm font-bold text-amber-900 dark:text-amber-200">Backup terjadwal</h2>
                <p class="mt-2 text-xs leading-5 text-amber-800 dark:text-amber-300">Aplikasi membuat backup otomatis setiap hari pukul 02.00 dan menghapus backup lokal yang berumur lebih dari 30 hari.</p>
                <p class="mt-3 text-[11px] leading-5 text-amber-700 dark:text-amber-400">Pastikan cron Laravel Scheduler aktif di server. Tanpa cron, backup otomatis tidak berjalan.</p>
                <pre class="mt-3 overflow-x-auto rounded-xl bg-amber-100/80 p-3 text-[10px] leading-5 text-amber-950 dark:bg-amber-950/60 dark:text-amber-100"><code>* * * * * cd /var/www/certifypro &amp;&amp; /usr/bin/php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code></pre>
            </article>
            <article class="rounded-2xl border border-sky-200 bg-sky-50 p-5 dark:border-sky-900/70 dark:bg-sky-950/30">
                <h2 class="text-sm font-bold text-sky-900 dark:text-sky-200">Simpan salinan di luar server</h2>
                <p class="mt-2 text-xs leading-5 text-sky-800 dark:text-sky-300">File di halaman ini masih berada di server yang sama. Unduh backup secara berkala atau atur penyimpanan off-site agar tetap tersedia jika server rusak.</p>
            </article>
            <p class="px-1 text-[11px] leading-5 text-slate-500 dark:text-slate-400">Pemulihan mengganti isi database. Sistem membuat backup pengaman secara otomatis sebelum memulihkan file pilihan.</p>
        </aside>
    </div>
</div>
