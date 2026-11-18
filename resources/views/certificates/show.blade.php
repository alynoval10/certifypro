<div>
    <style>
        @page { size: A4 landscape; margin: 7mm; }
        .certificate-sheet { width: min(100%, 1180px); min-height: 690px; }
        .certificate-frame { background: linear-gradient(135deg, var(--template-accent, #b58b45), #f4e0a6 18%, var(--template-accent, #b58b45) 49%, #e8d39b 78%, #a47c35); padding: 9px; }
        .certificate-inner { background: #fffefa; border: 1px solid var(--template-accent, #d8c18d); min-height: 672px; position: relative; overflow: hidden; }
        .certificate-inner::before, .certificate-inner::after { content: ""; position: absolute; width: 170px; height: 170px; border: 1px solid rgba(181,139,69,.16); border-radius: 50%; pointer-events: none; }
        .certificate-inner::before { left: -92px; top: -92px; box-shadow: 0 0 0 12px rgba(181,139,69,.04), 0 0 0 24px rgba(181,139,69,.04); }
        .certificate-inner::after { right: -92px; bottom: -92px; box-shadow: 0 0 0 12px rgba(181,139,69,.04), 0 0 0 24px rgba(181,139,69,.04); }
        .certificate-back { width: min(100%, 1180px); min-height: 690px; }
        @media print {
            html, body { background: #fff !important; }
            body { padding: 0 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .certificate-sheet { width: 100%; min-height: 0; max-width: none !important; }
            .certificate-frame { padding: 2.5mm; }
            .certificate-inner { min-height: 0; height: 190mm; }
            .certificate-back { width: 100%; min-height: 0; height: 190mm; break-before: page; page-break-before: always; }
            .no-print { display: none !important; }
            .certificate-inner, .certificate-frame { break-inside: avoid; }
        }
    </style>
    @unless ($batchMode ?? false)<div class="no-print mx-auto mb-5 flex max-w-5xl flex-wrap items-center justify-between gap-3"><a wire:navigate href="{{ route('certificates.index') }}" class="back-link">← Kembali ke daftar</a><div class="flex gap-2"><a href="{{ route('certificates.verify', $certificate->verification_code) }}" target="_blank" class="secondary-button">Lihat verifikasi</a><button type="button" onclick="window.print()" class="primary-button">Cetak depan &amp; belakang / Simpan PDF</button></div></div>
    @if (session('success'))<div class="no-print mx-auto mb-4 max-w-5xl rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">{{ session('success') }}</div>@endif @endunless
    <main class="certificate-sheet mx-auto">
        <div class="certificate-frame shadow-xl shadow-slate-300/70" style="--template-primary: {{ $template?->primary_color ?? '#1e1b4b' }}; --template-accent: {{ $template?->accent_color ?? '#b58b45' }}">
            <div class="certificate-inner flex flex-col px-8 py-7 text-center sm:px-14 sm:py-9">
                @if ($certificate->status === 'revoked')<div class="absolute right-7 top-7 z-10 rotate-6 rounded border-2 border-rose-600 px-3 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-rose-600">Dicabut</div>@endif
                <div class="relative z-10 flex items-center justify-center gap-4 border-b border-amber-700/25 pb-5">
                    @if ($school?->logo_path)<img src="{{ Storage::disk('public')->url($school->logo_path) }}" alt="Logo {{ $school->school_name }}" class="size-16 object-contain">@else<span class="flex size-14 items-center justify-center rounded-full border border-amber-600 text-lg font-extrabold text-indigo-950">{{ mb_strtoupper(mb_substr($school?->school_name ?? 'CP', 0, 2)) }}</span>@endif
                    <div class="text-left"><p class="text-[9px] font-bold uppercase tracking-[0.3em] text-amber-700">Dokumen resmi sekolah{{ $school?->npsn ? ' · NPSN '.$school->npsn : '' }}</p><h1 class="mt-1 text-lg font-extrabold uppercase tracking-[0.08em] text-indigo-950 sm:text-2xl">{{ $school?->school_name ?? 'Nama Sekolah' }}</h1>@if ($school?->address)<p class="mt-1 max-w-2xl text-[9px] text-slate-500">{{ $school->address }}{{ $school->phone ? ' · '.$school->phone : '' }}</p>@endif</div>
                </div>
                <div class="relative z-10 flex flex-1 flex-col items-center justify-center py-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.4em]" style="color: var(--template-accent)">Sertifikat</p>
                    <h2 class="mt-1 text-3xl font-black uppercase tracking-[0.14em] sm:text-4xl" style="color: var(--template-primary)">{{ $template?->front_heading ?? 'Sertifikat Praktik Kerja Industri' }}</h2>
                    <div class="mt-3 flex items-center gap-3"><span class="h-px w-12 bg-amber-600/60"></span><span class="size-2 rotate-45 border border-amber-600 bg-[#fffefa]"></span><span class="text-[10px] font-medium tracking-[0.16em] text-slate-500">{{ $certificate->certificate_number }}</span><span class="size-2 rotate-45 border border-amber-600 bg-[#fffefa]"></span><span class="h-px w-12 bg-amber-600/60"></span></div>
                    <p class="mt-5 text-xs text-slate-600">{{ $template?->front_subheading ?? 'Dengan ini menerangkan bahwa' }}</p>
                    <p class="mt-2 text-3xl font-bold tracking-wide text-slate-900 sm:text-4xl">{{ $certificate->internship->student->name }}</p>
                    <p class="mt-2 text-[11px] font-medium text-slate-600">NIS / NISN <strong class="text-slate-800">{{ $certificate->internship->student->student_number }}</strong><span class="mx-2 text-amber-600">•</span>{{ $certificate->internship->student->schoolClass?->name ?? $certificate->internship->student->class_name }}<span class="mx-2 text-amber-600">•</span>{{ $certificate->internship->student->majorRelation?->name ?? $certificate->internship->student->major }}</p>
                    <p class="mx-auto mt-5 max-w-3xl text-xs leading-6 text-slate-700">{{ $certificate->description ?: 'Telah menyelesaikan kegiatan Praktik Kerja Industri dengan baik.' }} Kegiatan dilaksanakan di <strong class="text-indigo-950">{{ $certificate->internship->company->name }}</strong>, pada {{ $certificate->internship->start_date->format('d/m/Y') }} sampai {{ $certificate->internship->end_date->format('d/m/Y') }}.</p>
                    <p class="mt-3 text-[10px] font-medium text-slate-600">Tahun ajaran <strong class="text-slate-800">{{ $certificate->internship->academicYear?->name ?? $certificate->internship->academic_year }}</strong></p>
                </div>
                <div class="relative z-10 mx-auto grid w-full max-w-3xl grid-cols-3 items-end gap-4 pb-4 text-center">
                    <div><p class="text-[9px] text-slate-500">{{ $template?->front_company_sign_label ?? 'Pimpinan DUDI' }}</p><div class="h-12"></div><p class="border-t border-slate-300 pt-2 text-[10px] font-bold text-slate-800">{{ $certificate->internship->company->leader_name ?: '________________________' }}</p></div>
                    <div class="flex flex-col items-center"><img src="{{ $qrCode }}" alt="Pindai QR untuk memverifikasi sertifikat {{ $certificate->certificate_number }}" class="size-20 bg-white p-1"><p class="mt-1 text-[8px] font-semibold text-slate-500">Pindai untuk verifikasi</p><p class="mt-1 text-[8px] text-slate-400">Terbit {{ $certificate->issued_at->format('d/m/Y') }}</p></div>
                    <div><p class="text-[9px] text-slate-500">{{ $template?->front_school_sign_label ?? 'Kepala Sekolah' }}</p><div class="h-12"></div><p class="border-t border-slate-300 pt-2 text-[10px] font-bold text-slate-800">{{ $school?->principal_name ?? '________________________' }}</p>@if ($school?->principal_nip)<p class="mt-1 text-[8px] text-slate-500">NIP. {{ $school->principal_nip }}</p>@endif</div>
                </div>
                <div class="relative z-10 flex flex-wrap items-center justify-between gap-2 border-t border-amber-700/20 pt-2 text-left text-[8px] text-slate-400"><span>Verifikasi digital: {{ route('certificates.verify', $certificate->verification_code) }}</span><span>Kode {{ mb_strtoupper(mb_substr($certificate->verification_code, 0, 8)) }}</span></div>
            </div>
        </div>
    </main>
    <main class="certificate-back mx-auto mt-6 sm:mt-8">
        <div class="certificate-frame h-full shadow-xl shadow-slate-300/70" style="--template-primary: {{ $template?->primary_color ?? '#1e1b4b' }}; --template-accent: {{ $template?->accent_color ?? '#b58b45' }}"><div class="certificate-inner flex h-full flex-col px-8 py-7 sm:px-14 sm:py-9">
            <div class="flex items-center justify-between gap-4 border-b border-amber-700/25 pb-4"><div><p class="text-[9px] font-bold uppercase tracking-[0.3em] text-amber-700">Lampiran sertifikat</p><h2 class="mt-1 text-xl font-extrabold uppercase tracking-[0.08em]" style="color: var(--template-primary)">{{ $template?->back_heading ?? 'Rincian Penilaian Prakerin' }}</h2></div><p class="text-right text-[10px] font-semibold text-slate-500">{{ $certificate->certificate_number }}</p></div>
            <div class="mt-4 grid gap-2 rounded-xl bg-slate-50 px-4 py-3 text-[10px] sm:grid-cols-2"><p><span class="text-slate-500">Nama peserta:</span> <strong>{{ $certificate->internship->student->name }}</strong></p><p><span class="text-slate-500">NIS / NISN:</span> <strong>{{ $certificate->internship->student->student_number }}</strong></p><p><span class="text-slate-500">Kelas / jurusan:</span> <strong>{{ $certificate->internship->student->schoolClass?->name ?? $certificate->internship->student->class_name }} · {{ $certificate->internship->student->majorRelation?->name ?? $certificate->internship->student->major }}</strong></p><p><span class="text-slate-500">Mitra industri:</span> <strong>{{ $certificate->internship->company->name }}</strong></p></div>
            <div class="mt-5 overflow-hidden rounded-xl border border-slate-200"><table class="w-full text-left text-[10px]"><thead class="bg-indigo-950 text-white"><tr><th class="px-4 py-3">Aspek penilaian</th><th class="px-4 py-3 text-right">Nilai</th><th class="px-4 py-3 text-right">Maksimum</th><th class="px-4 py-3 text-right">Bobot</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse ($certificate->internship->scores->filter(fn ($score) => $score->score !== null)->sortBy(fn ($score) => $score->component?->sort_order) as $score)<tr><td class="px-4 py-2.5 font-semibold text-slate-700">{{ $score->component?->name ?? 'Aspek penilaian' }}</td><td class="px-4 py-2.5 text-right font-semibold text-slate-800">{{ number_format((float) $score->score, 2, ',', '.') }}</td><td class="px-4 py-2.5 text-right text-slate-500">{{ number_format((float) ($score->component?->max_score ?? 100), 2, ',', '.') }}</td><td class="px-4 py-2.5 text-right text-slate-500">{{ number_format((float) ($score->component?->weight ?? 0), 2, ',', '.') }}%</td></tr>@empty<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Sertifikat ini menggunakan format nilai lama; rincian komponen tidak tersedia.</td></tr>@endforelse</tbody></table></div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-amber-600/30 bg-amber-50/60 px-5 py-4"><div><p class="text-[9px] font-bold uppercase tracking-[0.2em] text-amber-800">Nilai akhir berbobot</p><p class="mt-1 text-[10px] text-slate-500">Predikat {{ $certificate->predicate }}</p></div><p class="text-3xl font-black tracking-tight text-indigo-950">{{ $certificate->internship->score !== null ? number_format((float) $certificate->internship->score, 2, ',', '.') : '—' }}<span class="ml-1 text-sm font-semibold text-slate-400">/ 100</span></p></div>
            @if ($certificate->internship->final_note)<div class="mt-4 rounded-xl border border-slate-200 px-4 py-3"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Catatan pembimbing</p><p class="mt-1 text-[10px] leading-5 text-slate-600">{{ $certificate->internship->final_note }}</p></div>@endif
            <div class="mt-auto pt-6 text-center text-[9px] text-slate-500"><div class="mx-auto max-w-xs"><p>{{ $template?->back_mentor_sign_label ?? 'Pembimbing DUDI' }}</p><div class="h-16"></div><p class="border-t border-slate-300 pt-2 font-bold text-slate-800">{{ $certificate->internship->mentor_name ?: $certificate->internship->company->mentor_name ?: '________________________' }}</p></div></div>
            <div class="mt-3 border-t border-amber-700/20 pt-2 text-[8px] text-slate-400">Lampiran sah sebagai satu kesatuan dengan {{ $certificate->certificate_number }} · {{ route('certificates.verify', $certificate->verification_code) }}</div>
        </div></div>
    </main>
    @unless ($batchMode ?? false)
    @if ($certificate->status === 'issued')
        <section class="no-print mx-auto mt-5 max-w-5xl rounded-2xl border border-rose-200 bg-white p-5"><h2 class="text-xs font-bold text-rose-700">Cabut sertifikat</h2><p class="mt-1 text-[10px] text-slate-500">Pencabutan menandai sertifikat tidak berlaku pada halaman verifikasi.</p><form wire:submit="revoke" class="mt-3 flex flex-col gap-2 sm:flex-row"><input class="field-input flex-1" wire:model="revokeReason" maxlength="1000" placeholder="Alasan pencabutan" required>@error('revokeReason')<span class="field-error">{{ $message }}</span>@enderror<button type="submit" wire:confirm="Cabut sertifikat ini? Tindakan ini tidak dapat dibatalkan." class="rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-rose-700">Cabut sertifikat</button></form></section>
    @elseif ($certificate->revoke_reason)
        <section class="no-print mx-auto mt-5 max-w-5xl rounded-2xl border border-rose-200 bg-rose-50 p-5 text-xs text-rose-800"><strong>Alasan pencabutan:</strong> {{ $certificate->revoke_reason }}</section>
    @endif
    @endunless
</div>
