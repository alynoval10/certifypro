@extends('layouts.app')
@section('title', 'Peserta didik')
@section('breadcrumb', 'Peserta didik')
@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Data master</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Peserta didik</h1><p class="mt-1 text-xs text-slate-500">Kelola identitas siswa yang mengikuti Prakerin.</p></div><div class="flex flex-wrap gap-2"><a href="{{ route('students.template') }}" class="secondary-button">Unduh template Excel</a><a href="{{ route('students.create') }}" class="primary-button">＋ Tambah siswa</a></div></div>
    @include('partials.flash')
    @if (session('import_summary'))
        <div class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-xs text-indigo-900"><strong>Impor selesai:</strong> {{ session('import_summary.imported') }} siswa ditambahkan; {{ session('import_summary.skipped') }} baris dilewati.@if (session('import_summary.errors'))<ul class="mt-2 list-inside list-disc space-y-1">@foreach (session('import_summary.errors') as $error)<li>{{ $error }}</li>@endforeach</ul>@endif</div>
    @endif
    <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data" class="mt-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
        @csrf
        <label class="field-label flex-1">Impor dari Excel (.xlsx)<input class="field-input mt-2" type="file" name="spreadsheet" accept=".xlsx" required>@error('spreadsheet')<span class="field-error">{{ $message }}</span>@enderror</label>
        <button type="submit" class="secondary-button">Impor peserta didik</button>
        <p class="basis-full text-[10px] text-slate-500 sm:basis-auto sm:self-center">Maksimal 2.000 baris. Kelas/jurusan baru dari file akan ditambahkan ke data master.</p>
    </form>
    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/30">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs font-semibold text-slate-700">{{ number_format($students->total()) }} siswa terdaftar</p><div class="flex gap-2"><input class="search-input" name="q" value="{{ $search }}" placeholder="Cari nama, NIS, atau kelas..."><button class="secondary-button" type="submit">Cari</button></div></form>
        <div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Siswa</th><th>Kelas / Jurusan</th><th>Kontak</th><th>Riwayat Prakerin</th><th class="text-right">Aksi</th></tr></thead><tbody>
            @forelse ($students as $student)
                <tr><td><div class="font-semibold text-slate-800">{{ $student->name }}</div><div class="mt-1 text-[10px] text-slate-400">{{ $student->student_number }}</div></td><td><div class="text-slate-700">{{ $student->schoolClass?->name ?? $student->class_name }}</div><div class="mt-1 text-[10px] text-slate-400">{{ $student->majorRelation?->name ?? $student->major }}</div></td><td><div>{{ $student->email ?: '—' }}</div><div class="mt-1 text-[10px] text-slate-400">{{ $student->phone ?: '—' }}</div></td><td><span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-semibold text-indigo-700">{{ $student->internships_count }} catatan</span></td><td><div class="flex justify-end gap-2"><a class="table-action" href="{{ route('students.edit', $student) }}">Ubah</a><form method="POST" action="{{ route('students.destroy', $student) }}" data-confirm="Hapus data siswa {{ $student->name }}?">@csrf @method('DELETE')<button class="table-action text-rose-600" type="submit">Hapus</button></form></div></td></tr>
            @empty
                <tr><td colspan="5" class="empty-state"><strong>Belum ada data siswa</strong><span>Tambahkan peserta didik untuk mulai mencatat kegiatan Prakerin.</span><a href="{{ route('students.create') }}" class="mt-3 font-semibold text-indigo-600">Tambah siswa pertama →</a></td></tr>
            @endforelse
        </tbody></table></div>
        @if ($students->hasPages())<div class="border-t border-slate-100 px-5 py-3">{{ $students->links() }}</div>@endif
    </section>
@endsection
