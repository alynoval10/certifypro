@extends('layouts.app')

@section('title', 'Buat pengguna')
@section('breadcrumb', 'Buat pengguna')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('users.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">← Kembali ke pengguna</a>
        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Akses aplikasi</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Buat pengguna</h1>
            <p class="mt-1 text-xs text-slate-500">Isi data akun untuk memberi akses masuk ke CertifyPro.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" class="mt-6 grid gap-4">
                @csrf
                <label class="field-label">Nama lengkap<input class="field-input" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="255"></label>
                <label class="field-label">Email login<input class="field-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="255"></label>
                <label class="field-label">Kata sandi (minimal 10 karakter)<input class="field-input" type="password" name="password" autocomplete="new-password" required minlength="10"></label>
                <label class="field-label">Ulangi kata sandi<input class="field-input" type="password" name="password_confirmation" autocomplete="new-password" required minlength="10"></label>
                <div class="mt-2 flex justify-end gap-2 border-t border-slate-100 pt-5">
                    <a href="{{ route('users.index') }}" class="secondary-button">Batal</a>
                    <button type="submit" class="primary-button">Simpan pengguna</button>
                </div>
            </form>
        </div>
    </div>
@endsection
