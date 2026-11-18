@extends('layouts.app')
@section('title', 'Terbitkan sertifikat')
@section('breadcrumb', 'Sertifikat')
@section('content')
    <div class="mx-auto max-w-3xl"><a href="{{ route('certificates.index') }}" class="back-link">← Kembali ke sertifikat</a><div class="mt-4"><h1 class="text-2xl font-bold tracking-tight text-slate-900">Terbitkan sertifikat</h1><p class="mt-1 text-xs text-slate-500">Sertifikat hanya dapat diterbitkan untuk data Prakerin yang berstatus selesai.</p></div>@include('partials.flash')
        <form method="POST" action="{{ route('certificates.store') }}" class="form-card mt-6">
            @csrf
            <div class="grid gap-4">
                <label class="field-label">Peserta dan penempatan<select class="field-input" name="internship_id" required><option value="">Pilih data Prakerin yang selesai</option>@foreach ($internships as $internship)<option value="{{ $internship->id }}" @selected((string) old('internship_id', request('internship')) === (string) $internship->id)>{{ $internship->student->name }} · {{ $internship->student->student_number }} — {{ $internship->company->name }} ({{ $internship->academic_year }})</option>@endforeach</select>@error('internship_id')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Predikat<select class="field-input" name="predicate" required><option value="">Pilih predikat</option>@foreach (['Sangat Baik', 'Baik', 'Cukup'] as $predicate)<option value="{{ $predicate }}" @selected(old('predicate') === $predicate)>{{ $predicate }}</option>@endforeach</select>@error('predicate')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Keterangan pada sertifikat<textarea class="field-input min-h-28" name="description" maxlength="2000" placeholder="Telah menyelesaikan kegiatan Praktik Kerja Industri dengan baik...">{{ old('description', 'Telah menyelesaikan kegiatan Praktik Kerja Industri dengan baik dan memenuhi kompetensi yang ditetapkan.') }}</textarea>@error('description')<span class="field-error">{{ $message }}</span>@enderror</label>
            </div>
            <p class="mt-5 rounded-xl bg-amber-50 px-4 py-3 text-[10px] leading-5 text-amber-800">Nomor sertifikat dibuat otomatis. Pastikan identitas sekolah dan kepala sekolah di halaman Pengaturan sudah benar sebelum mencetak.</p>
            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5"><a href="{{ route('certificates.index') }}" class="secondary-button">Batal</a><button type="submit" class="primary-button">Terbitkan sertifikat</button></div>
        </form>
    </div>
@endsection
