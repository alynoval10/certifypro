<div>
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">Akses aplikasi</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Pengguna</h1>
            <p class="mt-1 text-xs text-slate-500">Buat dan kelola akun yang dapat masuk ke CertifyPro.</p>
        </div>
        <a href="{{ route('users.create') }}" class="primary-button">＋ Buat pengguna</a>
    </div>

    @include('partials.flash')

    <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-4">
            <p class="text-xs font-semibold text-slate-700">{{ number_format($users->total()) }} akun terdaftar</p>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Nama pengguna</th><th>Email</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="font-semibold text-slate-800">{{ $user->name }}@if ($user->id === auth()->id()) <span class="ml-1 rounded-full bg-indigo-50 px-2 py-1 text-[9px] font-semibold text-indigo-700">Anda</span>@endif</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold text-emerald-700">Aktif</span></td>
                            <td><div class="flex justify-end gap-2"><button class="table-action" wire:click="edit({{ $user->id }})">Ubah</button>@if ($user->id !== auth()->id())<button class="table-action text-rose-600" wire:click="delete({{ $user->id }})" wire:confirm="Hapus akun {{ $user->name }}?">Hapus</button>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state"><strong>Belum ada pengguna</strong><span>Buat akun untuk mulai menggunakan aplikasi.</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())<div class="border-t border-slate-100 px-5 py-3">{{ $users->links() }}</div>@endif
    </section>

    @if ($showForm)
        <div class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-slate-950/40 p-4" wire:click.self="closeForm">
            <section class="my-8 w-full max-w-xl rounded-2xl bg-white p-5 shadow-2xl sm:p-7">
                <div class="flex items-start justify-between">
                    <div><h2 class="text-lg font-bold text-slate-900">{{ $editingId ? 'Ubah pengguna' : 'Buat pengguna' }}</h2><p class="mt-1 text-xs text-slate-500">{{ $editingId ? 'Perbarui informasi akun. Kata sandi boleh dikosongkan jika tidak ingin diubah.' : 'Pengguna dapat masuk menggunakan email dan kata sandi ini.' }}</p></div>
                    <button type="button" wire:click="closeForm" class="rounded-lg px-3 py-1 text-slate-500" aria-label="Tutup">✕</button>
                </div>
                <form wire:submit="save" class="mt-5 grid gap-4">
                    <label class="field-label">Nama lengkap<input class="field-input" wire:model="form.name" autocomplete="name" required maxlength="255">@error('form.name')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Email login<input class="field-input" type="email" wire:model="form.email" autocomplete="email" required maxlength="255">@error('form.email')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">Kata sandi{{ $editingId ? ' baru (opsional)' : ' (minimal 10 karakter)' }}<input class="field-input" type="password" wire:model="form.password" autocomplete="new-password" @required(! $editingId) minlength="10">@error('form.password')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <div class="mt-2 flex justify-end gap-2 border-t border-slate-100 pt-5"><button type="button" wire:click="closeForm" class="secondary-button">Batal</button><button type="submit" class="primary-button" wire:loading.attr="disabled">Simpan pengguna</button></div>
                </form>
            </section>
        </div>
    @endif
</div>
