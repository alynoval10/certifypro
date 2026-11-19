<div>
    @include('partials.flash')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <div class="mb-3 flex flex-wrap items-center gap-2"><label for="dashboard-academic-year" class="sr-only">Pilih tahun ajaran</label><select id="dashboard-academic-year" wire:model.live="academicYearId" class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-[10px] font-semibold text-emerald-800 outline-none focus:ring-2 focus:ring-emerald-200 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200"><option value="">Tahun berjalan ({{ $activeAcademicYear?->name ?? 'belum dipilih' }})</option>@foreach ($academicYears as $year)<option value="{{ $year->id }}">{{ $year->name }}{{ $year->is_active ? ' · Aktif' : '' }}</option>@endforeach</select><a wire:navigate href="{{ route('school-structure.index') }}#tahun-ajaran" class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-medium text-slate-500 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">Kelola tahun</a></div>
            <h1 class="text-[26px] font-bold tracking-tight text-slate-900 sm:text-[32px]">Selamat datang, {{ auth()->user()->name }} <span class="inline-block origin-bottom animate-wave">👋</span></h1>
            <p class="mt-2 text-sm text-slate-500">Berikut ringkasan kegiatan Prakerin {{ $school?->school_name ? 'di '.$school->school_name : 'di sekolahmu' }}.</p>
        </div>
        <a wire:navigate href="{{ $pendingCertificateCount > 0 ? route('certificates.create') : route('internships.index') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-xs font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:-translate-y-0.5 hover:bg-indigo-700">
            <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Buat sertifikat
        </a>
    </div>

    <section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-6" aria-label="Statistik Prakerin">
        <article class="stat-card">
            <div class="flex items-start justify-between"><span class="stat-icon bg-indigo-50 text-indigo-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.75v4h4M8.5 13h7M8.5 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="rounded-full bg-indigo-50 px-2 py-1 text-[10px] font-semibold text-indigo-700">Semua status</span></div>
            <p class="mt-5 text-xs font-medium text-slate-500">Total sertifikat</p><div class="mt-1 flex items-end gap-2"><p class="text-[28px] font-bold tracking-tight text-slate-900">{{ number_format($certificateCount) }}</p><span class="mb-1 text-[10px] text-slate-400">dokumen</span></div><p class="mt-2 text-[10px] text-slate-400">Sertifikat yang sudah diterbitkan</p>
        </article>
        <article class="stat-card">
            <div class="flex items-start justify-between"><span class="stat-icon bg-amber-50 text-amber-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700">Prakerin selesai</span></div>
            <p class="mt-5 text-xs font-medium text-slate-500">Siap diterbitkan</p><div class="mt-1 flex items-end gap-2"><p class="text-[28px] font-bold tracking-tight text-slate-900">{{ number_format($pendingCertificateCount) }}</p><span class="mb-1 text-[10px] text-slate-400">sertifikat</span></div><p class="mt-2 text-[10px] text-slate-400">Prakerin selesai tanpa sertifikat</p>
        </article>
        <article class="stat-card">
            <div class="flex items-start justify-between"><span class="stat-icon bg-emerald-50 text-emerald-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><circle cx="9" cy="8" r="3.25" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19.5v-1.25A4.25 4.25 0 0 1 7.75 14h2.5a4.25 4.25 0 0 1 4.25 4.25v1.25M16 11.5l2 2 3.5-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-700">Prakerin aktif</span></div>
            <p class="mt-5 text-xs font-medium text-slate-500">Peserta Prakerin</p><div class="mt-1 flex items-end gap-2"><p class="text-[28px] font-bold tracking-tight text-slate-900">{{ number_format($studentCount) }}</p><span class="mb-1 text-[10px] text-slate-400">siswa</span></div><p class="mt-2 text-[10px] text-slate-400">Siswa dengan catatan Prakerin</p>
        </article>
        <article class="stat-card">
            <div class="flex items-start justify-between"><span class="stat-icon bg-sky-50 text-sky-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M3.5 20.25h17M5.5 20V7.5l6.5-3.75 6.5 3.75V20M9 20v-5.5h6V20M8.25 9h.01M12 9h.01M15.75 9h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="rounded-full bg-sky-50 px-2 py-1 text-[10px] font-semibold text-sky-700">Terdaftar</span></div>
            <p class="mt-5 text-xs font-medium text-slate-500">Mitra industri</p><div class="mt-1 flex items-end gap-2"><p class="text-[28px] font-bold tracking-tight text-slate-900">{{ number_format($companyCount) }}</p><span class="mb-1 text-[10px] text-slate-400">perusahaan</span></div><p class="mt-2 text-[10px] text-slate-400">Mitra yang menyambut peserta</p>
        </article>
        <article class="stat-card">
            <div class="flex items-start justify-between"><span class="stat-icon bg-emerald-50 text-emerald-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><circle cx="9" cy="8" r="3.25" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19.5v-1.25A4.25 4.25 0 0 1 7.75 14h2.5a4.25 4.25 0 0 1 4.25 4.25v1.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-700">Status siswa</span></div>
            <p class="mt-5 text-xs font-medium text-slate-500">Siswa aktif</p><div class="mt-1 flex items-end gap-2"><p class="text-[28px] font-bold tracking-tight text-slate-900">{{ number_format($activeStudentCount) }}</p><span class="mb-1 text-[10px] text-slate-400">siswa</span></div><p class="mt-2 text-[10px] text-slate-400">Peserta Prakerin tahun terpilih</p>
        </article>
        <article class="stat-card">
            <div class="flex items-start justify-between"><span class="stat-icon bg-slate-100 text-slate-600"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M5 4.5h14v15H5zM8 9h8M8 12.5h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-600">Status siswa</span></div>
            <p class="mt-5 text-xs font-medium text-slate-500">Siswa lulus</p><div class="mt-1 flex items-end gap-2"><p class="text-[28px] font-bold tracking-tight text-slate-900">{{ number_format($graduatedStudentCount) }}</p><span class="mb-1 text-[10px] text-slate-400">siswa</span></div><p class="mt-2 text-[10px] text-slate-400">Peserta Prakerin tahun terpilih</p>
        </article>
    </section>

    <section class="mt-6 grid gap-5 xl:grid-cols-[minmax(0,1.65fr)_minmax(300px,0.85fr)]">
        <article class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40">
            <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div><h2 class="text-sm font-bold text-slate-900">Sertifikat terbaru</h2><p class="mt-1 text-[11px] text-slate-400">Pantau proses penerbitan sertifikat Prakerin.</p></div>
                <a wire:navigate href="{{ route('certificates.index') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800">Lihat semua <span aria-hidden="true">→</span></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[610px] text-left">
                    <thead><tr class="bg-slate-50/70 text-[9px] font-bold uppercase tracking-[0.12em] text-slate-400"><th class="px-6 py-3.5">Nama siswa</th><th class="px-4 py-3.5">Tempat Prakerin</th><th class="px-4 py-3.5">Periode</th><th class="px-6 py-3.5 text-right">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($recentCertificates as $certificate)
                            @php($student = $certificate->internship->student)
                            <tr class="transition hover:bg-slate-50/60"><td class="px-6 py-4"><div class="flex items-center gap-3"><span class="avatar bg-indigo-100 text-indigo-700">{{ mb_strtoupper(mb_substr($student->name, 0, 2)) }}</span><span><span class="block font-semibold text-slate-800">{{ $student->name }}</span><span class="mt-0.5 block text-[10px] text-slate-400">{{ $student->class_name }} · {{ $student->student_number }}</span></span></div></td><td class="px-4 py-4 text-slate-600">{{ $certificate->internship->company->name }}</td><td class="px-4 py-4 text-slate-500">{{ $certificate->issued_at->format('d M Y') }}</td><td class="px-6 py-4 text-right"><span @class(['status-badge', 'bg-emerald-50 text-emerald-700' => $certificate->status === 'issued', 'bg-rose-50 text-rose-700' => $certificate->status === 'revoked'])><span @class(['size-1.5 rounded-full', 'bg-emerald-500' => $certificate->status === 'issued', 'bg-rose-500' => $certificate->status === 'revoked'])></span>{{ $certificate->status === 'issued' ? 'Terbit' : 'Dicabut' }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center"><p class="text-xs font-semibold text-slate-600">Belum ada sertifikat</p><p class="mt-1 text-[11px] text-slate-400">Sertifikat akan muncul di sini setelah diterbitkan.</p><a href="{{ route('internships.index') }}" class="mt-3 inline-flex text-[11px] font-semibold text-indigo-600">Lihat data Prakerin →</a></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 px-6 py-3.5 text-[10px] text-slate-400"><span>Menampilkan {{ $recentCertificates->count() }} dari {{ number_format($certificateCount) }} sertifikat</span><span>Data terbaru</span></div>
        </article>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-1">
            <article class="overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-700 p-6 text-white shadow-lg shadow-indigo-700/15">
                <div class="flex items-start justify-between"><div><p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-indigo-100/80">Progres semester</p><p class="mt-2 text-xl font-bold">Penerbitan sertifikat</p></div><span class="flex size-10 items-center justify-center rounded-xl bg-white/15"><svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="m5 12.5 4.2 4.2L19.5 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span></div>
                <div class="mt-7 flex items-end justify-between"><p class="text-4xl font-bold tracking-tight">{{ $progressPercentage }}<span class="text-2xl text-indigo-100">%</span></p><p class="pb-1 text-[10px] text-indigo-100/80">{{ number_format($issuedCount) }} dari {{ number_format($internshipCount) }} data</p></div>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-indigo-950/25"><div class="h-full rounded-full bg-gradient-to-r from-cyan-300 to-white" style="width: {{ $progressPercentage }}%"></div></div>
                <div class="mt-5 flex items-center gap-2 text-[10px] text-indigo-100/80"><span class="size-1.5 rounded-full bg-cyan-300"></span>Persentase Prakerin yang sudah bersertifikat</div>
            </article>
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/40 sm:col-span-1 xl:col-span-1">
                <div class="flex items-center justify-between"><div><h2 class="text-sm font-bold text-slate-900">Aktivitas terbaru</h2><p class="mt-1 text-[10px] text-slate-400">Pembaruan dari tim sekolah</p></div><button type="button" class="rounded-lg px-2 py-1 text-[10px] font-semibold text-slate-400 hover:bg-slate-50">Hari ini⌄</button></div>
                <div class="mt-5 grid gap-3">
                    <a wire:navigate href="{{ route('certificates.create') }}" class="flex items-center justify-between rounded-xl bg-amber-50/80 px-3.5 py-3 transition hover:bg-amber-50"><span><span class="block text-[11px] font-semibold text-slate-700">Sertifikat siap diterbitkan</span><span class="mt-1 block text-[10px] text-slate-500">Prakerin berstatus selesai</span></span><span class="rounded-lg bg-white px-2.5 py-1.5 text-xs font-bold text-amber-700 shadow-sm">{{ number_format($pendingCertificateCount) }}</span></a>
                    <a wire:navigate href="{{ route('internships.index', ['status' => 'ongoing']) }}" class="flex items-center justify-between rounded-xl bg-sky-50/80 px-3.5 py-3 transition hover:bg-sky-50"><span><span class="block text-[11px] font-semibold text-slate-700">Prakerin sedang berjalan</span><span class="mt-1 block text-[10px] text-slate-500">Peserta di mitra industri</span></span><span class="rounded-lg bg-white px-2.5 py-1.5 text-xs font-bold text-sky-700 shadow-sm">{{ number_format($ongoingCount) }}</span></a>
                    <a wire:navigate href="{{ route('internships.index', ['status' => 'completed']) }}" class="flex items-center justify-between rounded-xl bg-emerald-50/80 px-3.5 py-3 transition hover:bg-emerald-50"><span><span class="block text-[11px] font-semibold text-slate-700">Prakerin selesai</span><span class="mt-1 block text-[10px] text-slate-500">Catatan praktik disimpan</span></span><span class="rounded-lg bg-white px-2.5 py-1.5 text-xs font-bold text-emerald-700 shadow-sm">{{ number_format($completedCount) }}</span></a>
                </div>
            </article>
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-indigo-100/80 bg-indigo-50/60 p-5 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:px-6">
        <div class="flex items-start gap-3"><span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm"><svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true"><path d="m12 3 1.55 5.45L19 10l-5.45 1.55L12 17l-1.55-5.45L5 10l5.45-1.55L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></span><div><h2 class="text-xs font-bold text-slate-800">Semua proses Prakerin, satu tempat.</h2><p class="mt-1 text-[11px] leading-5 text-slate-500">CertifyPro membantu sekolah mengelola data peserta dan menerbitkan sertifikat dengan alur yang lebih tertata.</p></div></div>
        <a href="{{ route('about') }}" class="mt-4 inline-flex items-center gap-1 whitespace-nowrap text-[11px] font-semibold text-indigo-600 sm:mt-0">Pelajari lebih lanjut <span aria-hidden="true">→</span></a>
    </section>
</div>
