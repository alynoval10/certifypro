@extends('layouts.app')
@section('title', $student->exists ? 'Ubah siswa' : 'Tambah siswa')
@section('breadcrumb', 'Peserta didik')
@section('content')
    <div class="mx-auto max-w-3xl"><a href="{{ route('students.index') }}" class="back-link">← Kembali ke daftar siswa</a><div class="mt-4"><h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $student->exists ? 'Ubah data siswa' : 'Tambah siswa' }}</h1><p class="mt-1 text-xs text-slate-500">Isi identitas peserta didik sesuai data sekolah.</p></div>@include('partials.flash')
        <form method="POST" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" class="form-card mt-6">
            @csrf @if ($student->exists) @method('PUT') @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="field-label">NIS / NISN<input class="field-input" name="student_number" value="{{ old('student_number', $student->student_number) }}" required maxlength="30" autofocus>@error('student_number')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Nama lengkap<input class="field-input" name="name" value="{{ old('name', $student->name) }}" required maxlength="255">@error('name')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Kelas<select class="field-input" name="class_id" required><option value="">Pilih kelas</option>@foreach ($classes as $class)<option value="{{ $class->id }}" @selected((string) old('class_id', $student->class_id) === (string) $class->id)>{{ $class->name }}</option>@endforeach</select>@error('class_id')<span class="field-error">{{ $message }}</span>@enderror<a href="{{ route('school-structure.index') }}#kelas" class="mt-1 inline-block text-[10px] font-semibold text-indigo-600">Tambah kelas →</a></label>
                <label class="field-label">Program keahlian<select class="field-input" name="major_id" required><option value="">Pilih jurusan</option>@foreach ($majors as $major)<option value="{{ $major->id }}" @selected((string) old('major_id', $student->major_id) === (string) $major->id)>{{ $major->name }}</option>@endforeach</select>@error('major_id')<span class="field-error">{{ $message }}</span>@enderror<a href="{{ route('school-structure.index') }}#jurusan" class="mt-1 inline-block text-[10px] font-semibold text-indigo-600">Tambah jurusan →</a></label>
                <label class="field-label">Jenis kelamin<select class="field-input" name="gender"><option value="">Tidak diisi</option><option value="L" @selected(old('gender', $student->gender) === 'L')>Laki-laki</option><option value="P" @selected(old('gender', $student->gender) === 'P')>Perempuan</option></select></label>
                <label class="field-label">Email (opsional)<input class="field-input" type="email" name="email" value="{{ old('email', $student->email) }}" maxlength="255">@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Nomor telepon<input class="field-input" name="phone" value="{{ old('phone', $student->phone) }}" maxlength="30"></label>
                <label class="field-label sm:col-span-2">Alamat<textarea class="field-input min-h-24" name="address" maxlength="2000">{{ old('address', $student->address) }}</textarea></label>
            </div>
            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5"><a href="{{ route('students.index') }}" class="secondary-button">Batal</a><button type="submit" class="primary-button">{{ $student->exists ? 'Simpan perubahan' : 'Simpan siswa' }}</button></div>
        </form>
    </div>
@endsection
