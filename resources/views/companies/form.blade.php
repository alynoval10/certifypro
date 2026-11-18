@extends('layouts.app')
@section('title', $company->exists ? 'Ubah mitra' : 'Tambah mitra')
@section('breadcrumb', 'Mitra industri')
@section('content')
    <div class="mx-auto max-w-3xl"><a href="{{ route('companies.index') }}" class="back-link">← Kembali ke daftar mitra</a><div class="mt-4"><h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $company->exists ? 'Ubah data mitra' : 'Tambah mitra industri' }}</h1><p class="mt-1 text-xs text-slate-500">Simpan informasi perusahaan dan kontak pembimbing lapangan.</p></div>@include('partials.flash')
        <form method="POST" action="{{ $company->exists ? route('companies.update', $company) : route('companies.store') }}" class="form-card mt-6">
            @csrf @if ($company->exists) @method('PUT') @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="field-label sm:col-span-2">Nama perusahaan<input class="field-input" name="name" value="{{ old('name', $company->name) }}" required maxlength="255" autofocus>@error('name')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Bidang industri<input class="field-input" name="industry" value="{{ old('industry', $company->industry) }}" maxlength="120" placeholder="Teknologi informasi"></label>
                <label class="field-label">Nama kontak/pembimbing<input class="field-input" name="contact_name" value="{{ old('contact_name', $company->contact_name) }}" maxlength="255"></label>
                <label class="field-label">Email<input class="field-input" type="email" name="email" value="{{ old('email', $company->email) }}" maxlength="255">@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">Nomor telepon<input class="field-input" name="phone" value="{{ old('phone', $company->phone) }}" maxlength="30"></label>
                <label class="field-label sm:col-span-2">Alamat<textarea class="field-input min-h-24" name="address" maxlength="2000">{{ old('address', $company->address) }}</textarea></label>
            </div>
            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5"><a href="{{ route('companies.index') }}" class="secondary-button">Batal</a><button type="submit" class="primary-button">{{ $company->exists ? 'Simpan perubahan' : 'Simpan mitra' }}</button></div>
        </form>
    </div>
@endsection
