@extends('layouts.app')
@section('title', 'Mitra industri')
@section('breadcrumb', 'Mitra industri')
@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Data master</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Mitra industri</h1><p class="mt-1 text-xs text-slate-500">Kelola perusahaan dan kontak pembimbing lapangan.</p></div><a href="{{ route('companies.create') }}" class="primary-button">＋ Tambah mitra</a></div>
    @include('partials.flash')
    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/30">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs font-semibold text-slate-700">{{ number_format($companies->total()) }} mitra terdaftar</p><div class="flex gap-2"><input class="search-input" name="q" value="{{ $search }}" placeholder="Cari nama atau bidang industri..."><button class="secondary-button" type="submit">Cari</button></div></form>
        <div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Perusahaan</th><th>Kontak pembimbing</th><th>Alamat kontak</th><th>Peserta Prakerin</th><th class="text-right">Aksi</th></tr></thead><tbody>
            @forelse ($companies as $company)
                <tr><td><div class="font-semibold text-slate-800">{{ $company->name }}</div><div class="mt-1 text-[10px] text-slate-400">{{ $company->industry ?: 'Bidang belum diisi' }}</div></td><td>{{ $company->contact_name ?: '—' }}</td><td><div>{{ $company->phone ?: '—' }}</div><div class="mt-1 text-[10px] text-slate-400">{{ $company->email ?: '' }}</div></td><td><span class="rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-semibold text-sky-700">{{ $company->internships_count }} peserta</span></td><td><div class="flex justify-end gap-2"><a class="table-action" href="{{ route('companies.edit', $company) }}">Ubah</a><form method="POST" action="{{ route('companies.destroy', $company) }}" data-confirm="Hapus mitra {{ $company->name }}?">@csrf @method('DELETE')<button class="table-action text-rose-600" type="submit">Hapus</button></form></div></td></tr>
            @empty
                <tr><td colspan="5" class="empty-state"><strong>Belum ada mitra industri</strong><span>Tambahkan perusahaan tempat siswa menjalani Prakerin.</span><a href="{{ route('companies.create') }}" class="mt-3 font-semibold text-indigo-600">Tambah mitra pertama →</a></td></tr>
            @endforelse
        </tbody></table></div>
        @if ($companies->hasPages())<div class="border-t border-slate-100 px-5 py-3">{{ $companies->links() }}</div>@endif
    </section>
@endsection
