<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public function mount(?Company $company = null): void
    {
        $this->resetForm();
        if (request()->routeIs('companies.create')) {
            $this->create();
        } elseif ($company?->exists) {
            $this->edit($company->id);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $companyId): void
    {
        $company = Company::query()->findOrFail($companyId);
        $this->editingId = $company->id;
        $this->form = $company->only(['name', 'industry', 'contact_name', 'email', 'phone', 'address']);
        $this->showForm = true;
    }

    public function save(): void
    {
        $company = $this->editingId ? Company::query()->findOrFail($this->editingId) : new Company;
        $validated = $this->validate([
            'form.name' => ['required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($company)],
            'form.industry' => ['nullable', 'string', 'max:120'],
            'form.contact_name' => ['nullable', 'string', 'max:255'],
            'form.email' => ['nullable', 'email', 'max:255'],
            'form.phone' => ['nullable', 'string', 'max:30'],
            'form.address' => ['nullable', 'string', 'max:2000'],
        ])['form'];
        $company->fill($validated)->save();
        $this->showForm = false;
        $this->resetForm();
        session()->flash('success', 'Data mitra berhasil disimpan.');

        $this->redirectRoute('companies.index');
    }

    public function delete(int $companyId): void
    {
        $company = Company::query()->findOrFail($companyId);
        if ($company->internships()->exists()) {
            session()->flash('error', 'Mitra memiliki riwayat Prakerin dan tidak dapat dihapus.');

            return;
        }
        $company->delete();
        session()->flash('success', 'Data mitra berhasil dihapus.');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
        if (request()->routeIs('companies.create', 'companies.edit')) {
            $this->redirectRoute('companies.index');

            return;
        }
    }

    public function render(): View
    {
        $search = trim($this->search);

        return view('livewire.companies.index', [
            'companies' => Company::query()->withCount('internships')
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(fn (Builder $companies) => $companies
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('industry', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%"));
                })
                ->orderBy('name')->paginate(12),
        ])->layout('layouts.app', ['title' => 'Mitra industri', 'breadcrumb' => 'Mitra industri']);
    }

    private function resetForm(): void
    {
        $this->form = ['name' => '', 'industry' => '', 'contact_name' => '', 'email' => '', 'phone' => '', 'address' => ''];
    }
}
