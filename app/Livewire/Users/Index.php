<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $this->editingId = $user->id;
        $this->form = ['name' => $user->name, 'email' => $user->email, 'password' => ''];
        $this->showForm = true;
    }

    public function save(): void
    {
        $user = $this->editingId ? User::query()->findOrFail($this->editingId) : new User;
        $validated = $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'form.password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:6', 'max:255'],
        ])['form'];

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($validated['password'] !== null && $validated['password'] !== '') {
            $user->password = $validated['password'];
        }
        $user->save();

        $this->closeForm();
        session()->flash('success', 'Akun pengguna berhasil disimpan.');
    }

    public function delete(int $userId): void
    {
        abort_if($userId === Auth::id(), 403);

        $user = User::query()->findOrFail($userId);
        if (User::query()->count() <= 1) {
            session()->flash('error', 'Akun terakhir tidak dapat dihapus.');

            return;
        }

        $user->delete();
        session()->flash('success', 'Akun pengguna berhasil dihapus.');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.users.index', [
            'users' => User::query()->orderBy('name')->paginate(12),
        ])->layout('layouts.app', ['title' => 'Pengguna', 'breadcrumb' => 'Pengguna']);
    }

    private function resetForm(): void
    {
        $this->form = ['name' => '', 'email' => '', 'password' => ''];
    }
}
