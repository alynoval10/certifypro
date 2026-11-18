@extends('layouts.app')
@section('title', 'Sertifikat')
@section('breadcrumb', 'Sertifikat')
@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Dokumen kelulusan</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Sertifikat Prakerin</h1><p class="mt-1 text-xs text-slate-500">Terbitkan, cetak, dan verifikasi sertifikat peserta.</p></div><a href="{{ route('certificates.create') }}" class="primary-button">＋ Terbitkan sertifikat</a></div>
    @include('partials.flash')
    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/30">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs font-semibold text-slate-700">{{ number_format($certificates->total()) }} sertifikat</p><div class="flex flex-wrap gap-2"><input class="search-input" name="q" value="{{ $search }}" placeholder="Cari nama atau nomor sertifikat..."><select class="search-input" name="status"><option value="">Semua status</option><option value="issued" @selected($status === 'issued')>Terbit</option><option value="revoked" @selected($status === 'revoked')>Dicabut</option></select><button class="secondary-button" type="submit">Filter</button></div></form>
        <div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Nomor sertifikat</th><th>Peserta didik</th><th>Tempat Prakerin</th><th>Tanggal terbit</th><th>Status</th><th class="text-right">Dokumen</th></tr></thead><tbody>
            @forelse ($certificates as $certificate)
                <tr><td><div class="font-semibold text-indigo-700">{{ $certificate->certificate_number }}</div><div class="mt-1 text-[10px] text-slate-400">Predikat {{ $certificate->predicate }}</div></td><td><div class="font-semibold text-slate-800">{{ $certificate->internship->student->name }}</div><div class="mt-1 text-[10px] text-slate-400">{{ $certificate->internship->student->student_number }} · {{ $certificate->internship->student->class_name }}</div></td><td>{{ $certificate->internship->company->name }}</td><td>{{ $certificate->issued_at->format('d M Y') }}</td><td><span @class(['status-badge', 'bg-emerald-50 text-emerald-700' => $certificate->status === 'issued', 'bg-rose-50 text-rose-700' => $certificate->status === 'revoked'])>{{ $certificate->status === 'issued' ? 'Terbit' : 'Dicabut' }}</span></td><td><div class="flex justify-end"><a class="table-action" href="{{ route('certificates.show', $certificate) }}">Lihat / cetak</a></div></td></tr>
            @empty
                <tr><td colspan="6" class="empty-state"><strong>Belum ada sertifikat terbit</strong><span>Selesaikan data Prakerin terlebih dahulu, lalu terbitkan sertifikat.</span><a href="{{ route('internships.index') }}" class="mt-3 font-semibold text-indigo-600">Lihat data Prakerin →</a></td></tr>
            @endforelse
        </tbody></table></div>
        @if ($certificates->hasPages())<div class="border-t border-slate-100 px-5 py-3">{{ $certificates->links() }}</div>@endif
    </section>
@endsection
