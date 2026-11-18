<div>
    <div class="mx-auto max-w-5xl">
        <a wire:navigate href="{{ route('certificates.index') }}" class="back-link">← Kembali ke daftar sertifikat</a>
        <div class="mt-4"><h1 class="text-2xl font-bold tracking-tight text-slate-900">Terbitkan sertifikat</h1><p class="mt-1 text-xs text-slate-500">Pilih beberapa siswa sekaligus. Nilai akhir dihitung dari semua komponen aktif dan rincian nilai dicetak di halaman belakang.</p></div>

        <form wire:submit="issue" class="form-card mt-6 space-y-5">
            <section>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <label class="field-label flex-1">Cari siswa atau mitra<input class="field-input" wire:model.live.debounce.300ms="search" placeholder="Nama, NIS, atau nama DUDI..."></label>
                    <div class="flex gap-2 pb-1"><button type="button" wire:click="selectAllEligible" class="secondary-button">Pilih semua</button><button type="button" wire:click="clearSelection" class="secondary-button">Kosongkan</button></div>
                </div>
                @error('selectedInternshipIds')<span class="field-error">{{ $message }}</span>@enderror
                <div class="mt-3 overflow-hidden rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3 text-xs"><strong class="text-slate-800">Prakerin selesai dan nilai lengkap</strong><span class="rounded-full bg-indigo-100 px-2.5 py-1 font-semibold text-indigo-700">{{ $selectedCount }} dipilih</span></div>
                    @if ($internships->isEmpty())
                        <div class="px-5 py-10 text-center">
                            @if (trim($search) !== '')
                                <p class="text-sm font-semibold text-slate-700">Tidak ditemukan siswa siap terbit untuk “{{ $search }}”</p>
                                <p class="mt-1 text-xs text-slate-500">Coba cari dengan nama siswa, NIS, atau nama DUDI yang lain.</p>
                                <button type="button" wire:click="clearSearch" class="mt-3 text-xs font-semibold text-indigo-600">Hapus pencarian</button>
                            @else
                                <p class="text-sm font-semibold text-slate-700">Belum ada siswa yang siap menerima sertifikat</p>
                                <p class="mt-1 text-xs text-slate-500">Siswa harus sudah menyelesaikan Prakerin, memiliki nilai akhir, dan seluruh komponen nilai aktif harus terisi.</p>
                                <a wire:navigate href="{{ route('assessments.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-600">Periksa penilaian →</a>
                            @endif
                        </div>
                    @else
                        <div class="max-h-[26rem] overflow-auto">
                            <table class="data-table w-full min-w-[700px]"><thead class="sticky top-0 bg-white"><tr><th class="w-10"></th><th>Siswa</th><th>DUDI</th><th>Tahun ajaran</th><th class="text-right">Nilai akhir</th></tr></thead><tbody>
                                @foreach ($internships as $internship)
                                    <tr wire:key="issue-internship-{{ $internship->id }}">
                                        <td><input type="checkbox" value="{{ $internship->id }}" wire:model.live="selectedInternshipIds" aria-label="Pilih {{ $internship->student->name }}"></td>
                                        <td><div class="font-semibold text-slate-800">{{ $internship->student->name }}</div><div class="mt-1 text-[10px] text-slate-400">{{ $internship->student->student_number }} · {{ $internship->student->schoolClass?->name ?? $internship->student->class_name }}</div></td>
                                        <td>{{ $internship->company->name }}</td><td>{{ $internship->academicYear?->name ?? $internship->academic_year }}</td>
                                        <td class="text-right"><strong>{{ number_format((float) $internship->score, 2, ',', '.') }}</strong><div class="mt-1 text-[10px] text-slate-400">Selesai {{ $internship->end_date->format('d/m/Y') }}</div></td>
                                    </tr>
                                @endforeach
                            </tbody></table>
                        </div>
                    @endif
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2">
                <label class="field-label">Awalan nomor sertifikat<input class="field-input" wire:model.live="numberPrefix" maxlength="50" placeholder="SMKN1Krangkeng/TKJ/PKL">@error('numberPrefix')<span class="field-error">{{ $message }}</span>@enderror<p class="mt-1 text-[10px] font-normal text-slate-500">Contoh: {{ trim($numberPrefix, '/') ?: 'SMKN1Krangkeng/TKJ/PKL' }}/001</p></label>
                <label class="field-label">Nomor urut awal<input class="field-input" type="number" wire:model.live="numberStart" min="1" max="999999">@error('numberStart')<span class="field-error">{{ $message }}</span>@enderror<p class="mt-1 text-[10px] font-normal text-slate-500">Nomor berikutnya akan bertambah otomatis untuk setiap siswa.</p></label>
            </section>
            <label class="field-label">Keterangan sertifikat<textarea class="field-input min-h-24" wire:model="description" maxlength="2000" placeholder="Telah menyelesaikan kegiatan Praktik Kerja Industri dengan baik."></textarea>@error('description')<span class="field-error">{{ $message }}</span>@enderror</label>
            <p class="rounded-xl bg-amber-50 px-4 py-3 text-[10px] leading-5 text-amber-800">Nomor sertifikat akan dibuat berurutan untuk siswa terpilih. Pastikan identitas dan nilai sudah benar; sertifikat yang diterbitkan tidak dapat diubah.</p>
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-5"><a wire:navigate href="{{ route('certificates.index') }}" class="secondary-button">Batal</a><button type="submit" wire:loading.attr="disabled" @disabled($selectedCount === 0) class="primary-button"><span wire:loading.remove wire:target="issue">Terbitkan {{ $selectedCount }} sertifikat</span><span wire:loading wire:target="issue">Menerbitkan…</span></button></div>
        </form>
    </div>
</div>
