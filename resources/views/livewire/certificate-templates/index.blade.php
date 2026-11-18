<div>
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Desain dokumen</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Template sertifikat</h1><p class="mt-1 text-xs text-slate-500">Buat beberapa desain dan pilih desain default untuk sertifikat baru. Sertifikat lama mempertahankan template yang dipakai saat diterbitkan.</p></div>
        <button wire:click="create" class="primary-button">＋ Buat template</button>
    </div>
    @include('partials.flash')
    <div class="mt-6 grid gap-4 xl:grid-cols-2">
        @forelse ($templates as $template)
            <article wire:key="template-{{ $template->id }}" class="overflow-hidden rounded-2xl border {{ $template->is_active ? 'border-indigo-300 ring-2 ring-indigo-100' : 'border-slate-200' }} bg-white shadow-sm">
                <div class="h-2" style="background: linear-gradient(90deg, {{ $template->primary_color }}, {{ $template->accent_color }});"></div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3"><div><div class="flex items-center gap-2"><h2 class="text-sm font-bold text-slate-900">{{ $template->name }}</h2>@if ($template->is_active)<span class="rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-bold text-emerald-700">Aktif</span>@endif</div><p class="mt-1 text-[10px] text-slate-500">{{ $template->certificates_count }} sertifikat menggunakan template ini</p></div><div class="flex gap-2">@if (! $template->is_active)<button class="table-action" wire:click="activate({{ $template->id }})">Jadikan default</button>@endif<button class="table-action" wire:click="edit({{ $template->id }})">Ubah</button>@if (! $template->is_active && $template->certificates_count === 0)<button class="table-action text-rose-600" wire:click="delete({{ $template->id }})" wire:confirm="Hapus template {{ $template->name }}?">Hapus</button>@endif</div></div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border-4 p-4 text-center" style="border-color: {{ $template->accent_color }}"><p class="text-[9px] font-bold uppercase tracking-widest" style="color: {{ $template->accent_color }}">{{ $template->name }}</p><h3 class="mt-3 text-xs font-extrabold uppercase" style="color: {{ $template->primary_color }}">{{ $template->front_heading }}</h3><p class="mt-2 text-[9px] text-slate-500">{{ $template->front_subheading }}</p><p class="mt-5 border-t pt-2 text-[9px] font-semibold" style="border-color: {{ $template->accent_color }}; color: {{ $template->primary_color }}">{{ $template->front_company_sign_label }} · {{ $template->front_school_sign_label }}</p></div>
                        <div class="rounded-xl border-4 p-4 text-center" style="border-color: {{ $template->accent_color }}"><p class="text-[9px] font-bold uppercase tracking-widest" style="color: {{ $template->accent_color }}">Lampiran penilaian</p><h3 class="mt-3 text-xs font-extrabold uppercase" style="color: {{ $template->primary_color }}">{{ $template->back_heading }}</h3><div class="mt-3 space-y-1"><div class="h-1 rounded bg-slate-100"></div><div class="h-1 rounded bg-slate-100"></div><div class="h-1 rounded bg-slate-100"></div></div><p class="mt-5 border-t pt-2 text-[9px] font-semibold" style="border-color: {{ $template->accent_color }}; color: {{ $template->primary_color }}">{{ $template->back_mentor_sign_label }}</p></div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-xs text-slate-500">Belum ada template sertifikat.</div>
        @endforelse
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-slate-950/40 p-4" wire:click.self="closeForm">
            <section class="my-8 w-full max-w-3xl rounded-2xl bg-white p-5 shadow-2xl sm:p-7">
                <div class="flex items-start justify-between"><div><h2 class="text-lg font-bold text-slate-900">{{ $editingId ? 'Ubah template' : 'Buat template baru' }}</h2><p class="mt-1 text-xs text-slate-500">Sesuaikan tulisan, label tanda tangan, serta warna utama dan aksen.</p></div><button wire:click="closeForm" class="rounded-lg px-3 py-1 text-slate-500">✕</button></div>
                <form wire:submit="save" class="mt-5 grid gap-4 sm:grid-cols-2">
                    <label class="field-label sm:col-span-2">Nama template<input class="field-input" wire:model="form.name" required maxlength="120">@error('form.name')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Judul halaman depan<input class="field-input" wire:model="form.front_heading" required maxlength="160">@error('form.front_heading')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Kalimat pembuka<input class="field-input" wire:model="form.front_subheading" required maxlength="255">@error('form.front_subheading')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Judul halaman belakang<input class="field-input" wire:model="form.back_heading" required maxlength="160">@error('form.back_heading')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Label tanda tangan DUDI depan<input class="field-input" wire:model="form.front_company_sign_label" required maxlength="120">@error('form.front_company_sign_label')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Label tanda tangan sekolah<input class="field-input" wire:model="form.front_school_sign_label" required maxlength="120">@error('form.front_school_sign_label')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Label tanda tangan DUDI belakang<input class="field-input" wire:model="form.back_mentor_sign_label" required maxlength="120">@error('form.back_mentor_sign_label')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Warna utama<input class="field-input h-12 p-1" type="color" wire:model="form.primary_color">@error('form.primary_color')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Warna aksen<input class="field-input h-12 p-1" type="color" wire:model="form.accent_color">@error('form.accent_color')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <div class="mt-2 flex justify-end gap-2 border-t border-slate-100 pt-5 sm:col-span-2"><button type="button" wire:click="closeForm" class="secondary-button">Batal</button><button type="submit" class="primary-button">Simpan template</button></div>
                </form>
            </section>
        </div>
    @endif
</div>
