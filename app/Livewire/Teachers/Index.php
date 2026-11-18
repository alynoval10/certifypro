<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use App\Services\TeacherSpreadsheetService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public $spreadsheet = null;

    /** @var array{imported: int, skipped: int, errors: list<string>}|null */
    public ?array $importSummary = null;

    public function mount(): void
    {
        $this->resetForm();
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

    public function edit(int $teacherId): void
    {
        $teacher = Teacher::query()->findOrFail($teacherId);
        $this->editingId = $teacher->id;
        $this->form = $teacher->only(['name', 'nip', 'email', 'phone']);
        $this->showForm = true;
    }

    public function save(): void
    {
        $teacher = $this->editingId ? Teacher::query()->findOrFail($this->editingId) : new Teacher;
        $validated = $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.nip' => ['nullable', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore($teacher)],
            'form.email' => ['nullable', 'email', 'max:255'],
            'form.phone' => ['nullable', 'string', 'max:30'],
        ])['form'];
        $teacher->fill($validated)->save();
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
        session()->flash('success', 'Data guru pembimbing berhasil disimpan.');
    }

    public function delete(int $teacherId): void
    {
        $teacher = Teacher::query()->findOrFail($teacherId);
        if ($teacher->internships()->exists()) {
            session()->flash('error', 'Guru ini sudah terhubung dengan catatan Prakerin dan tidak dapat dihapus.');

            return;
        }
        $teacher->delete();
        session()->flash('success', 'Data guru pembimbing berhasil dihapus.');
    }

    public function importSpreadsheet(): void
    {
        $this->validate(['spreadsheet' => ['required', 'file', 'extensions:xlsx', 'max:10240']]);
        $this->importSummary = app(TeacherSpreadsheetService::class)->import($this->spreadsheet);
        $this->reset('spreadsheet');
        $this->resetPage();
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
        $search = trim($this->search);

        return view('livewire.teachers.index', [
            'teachers' => Teacher::query()->withCount('internships')
                ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%"))
                ->orderBy('name')->paginate(12),
        ])->layout('layouts.app', ['title' => 'Guru pembimbing', 'breadcrumb' => 'Guru pembimbing']);
    }

    private function resetForm(): void
    {
        $this->form = ['name' => '', 'nip' => '', 'email' => '', 'phone' => ''];
    }
}
