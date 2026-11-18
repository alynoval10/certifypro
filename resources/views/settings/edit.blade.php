@extends('layouts.app')
@section('title', 'Pengaturan sekolah')
@section('breadcrumb', 'Pengaturan sekolah')
@section('content')
    <div class="mx-auto max-w-4xl"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Pengaturan</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Identitas sekolah</h1><p class="mt-1 text-xs text-slate-500">Informasi ini dicetak pada sertifikat Prakerin.</p>@include('partials.flash')
        <form method="POST" action="{{ route('settings.update') }}" class="form-card mt-6" enctype="multipart/form-data">@csrf @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="field-label sm:col-span-2">Nama resmi sekolah<input class="field-input" name="school_name" value="{{ old('school_name', $school->school_name) }}" required maxlength="255">@error('school_name')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label sm:col-span-2">Logo sekolah<input class="field-input file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-[10px] file:font-semibold file:text-indigo-700" type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">@error('logo')<span class="field-error">{{ $message }}</span>@enderror<span class="text-[10px] font-normal text-slate-400">PNG, JPG, or WebP · maksimal 2 MB. Logo akan muncul pada sertifikat.</span>@if ($school->logo_path)<span class="text-[10px] font-medium text-emerald-700">Logo sudah tersimpan.</span>@endif</label>
                <label class="field-label">NPSN<input class="field-input" name="npsn" value="{{ old('npsn', $school->npsn) }}" maxlength="20"></label>
                <label class="field-label">Email sekolah<input class="field-input" type="email" name="email" value="{{ old('email', $school->email) }}" maxlength="255">@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Telepon<input class="field-input" name="phone" value="{{ old('phone', $school->phone) }}" maxlength="30"></label>
                <label class="field-label">Nama kepala sekolah<input class="field-input" name="principal_name" value="{{ old('principal_name', $school->principal_name) }}" required maxlength="120">@error('principal_name')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">NIP kepala sekolah<input class="field-input" name="principal_nip" value="{{ old('principal_nip', $school->principal_nip) }}" maxlength="30"></label>
                <label class="field-label sm:col-span-2">Alamat sekolah<textarea class="field-input min-h-24" name="address" maxlength="2000">{{ old('address', $school->address) }}</textarea></label>
            </div>
            <div class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between"><p class="text-[10px] leading-5 text-slate-400">Periksa nama sekolah dan kepala sekolah sebelum menerbitkan sertifikat.</p><button type="submit" class="primary-button">Simpan identitas</button></div>
        </form>
    </div>
@endsection
