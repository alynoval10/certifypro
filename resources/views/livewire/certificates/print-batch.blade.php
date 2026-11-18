<div>
    <style>
        @media print {
            .batch-entry:not(:last-child) .certificate-back { break-after: page; page-break-after: always; }
        }
    </style>
    <div class="no-print mx-auto mb-6 flex max-w-5xl flex-wrap items-center justify-between gap-3 rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm">
        <div><h1 class="text-sm font-bold text-slate-900">Cetak {{ $certificates->count() }} sertifikat</h1><p class="mt-1 text-[10px] text-slate-500">Setiap sertifikat berisi halaman depan dan belakang. Untuk hasil bolak-balik, pilih pencetakan dua sisi dengan membalik pada sisi panjang.</p></div>
        <div class="flex gap-2"><a href="{{ route('certificates.index') }}" class="secondary-button">Kembali</a><button type="button" onclick="window.print()" class="primary-button">Cetak depan &amp; belakang</button></div>
    </div>
    @foreach ($certificates as $certificate)
        @php
            $template = $certificate->template ?? $activeTemplate;
            $qrCode = $qrCodes[$certificate->id];
            $batchMode = true;
        @endphp
        <div class="batch-entry">
            @include('certificates.show')
        </div>
    @endforeach
</div>
