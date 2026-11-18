<?php

namespace App\Livewire\Students;

use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentImportService;
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

    public function mount(?Student $student = null): void
    {
        $this->resetForm();
        if (request()->routeIs('students.create')) {
            $this->create();
        } elseif ($student?->exists) {
            $this->edit($student->id);
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

    public function edit(int $studentId): void
    {
        $student = Student::query()->findOrFail($studentId);
        $this->editingId = $student->id;
        $this->form = $student->only(['student_number', 'name', 'class_id', 'major_id', 'gender', 'email', 'phone', 'address']);
        $this->showForm = true;
    }

    public function save(): void
    {
        $student = $this->editingId ? Student::query()->findOrFail($this->editingId) : new Student;
        $validated = $this->validate([
            'form.student_number' => ['required', 'string', 'max:30', Rule::unique('students', 'student_number')->ignore($student)],
            'form.name' => ['required', 'string', 'max:255'],
            'form.class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'form.major_id' => ['required', 'integer', 'exists:majors,id'],
            'form.gender' => ['nullable', Rule::in(['L', 'P'])],
            'form.email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student)],
            'form.phone' => ['nullable', 'string', 'max:30'],
            'form.address' => ['nullable', 'string', 'max:2000'],
        ])['form'];

        $student->fill($validated);
        $student->class_name = SchoolClass::query()->findOrFail($validated['class_id'])->name;
        $student->major = Major::query()->findOrFail($validated['major_id'])->name;
        $student->save();

        $this->showForm = false;
        $this->resetForm();
        session()->flash('success', 'Data peserta didik berhasil disimpan.');

        $this->redirectRoute('students.index');
    }

    public function delete(int $studentId): void
    {
        $student = Student::query()->findOrFail($studentId);
        if ($student->internships()->exists()) {
            session()->flash('error', 'Siswa memiliki riwayat Prakerin dan tidak dapat dihapus.');

            return;
        }
        $student->delete();
        session()->flash('success', 'Data peserta didik berhasil dihapus.');
    }

    public function importSpreadsheet(): void
    {
        $this->validate(['spreadsheet' => ['required', 'file', 'extensions:xlsx', 'max:10240']]);
        $this->importSummary = app(StudentImportService::class)->import($this->spreadsheet);
        $this->reset('spreadsheet');
        $this->resetPage();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
        if (request()->routeIs('students.create', 'students.edit')) {
            $this->redirectRoute('students.index');

            return;
        }
    }

    public function render(): View
    {
        $search = trim($this->search);
        $students = Student::query()
            ->with(['schoolClass', 'majorRelation'])
            ->withCount('internships')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $students) use ($search): void {
                    $students->where('name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%")
                        ->orWhereHas('schoolClass', fn (Builder $classes) => $classes->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('majorRelation', fn (Builder $majors) => $majors->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.students.index', [
            'students' => $students,
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'majors' => Major::query()->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Peserta didik', 'breadcrumb' => 'Peserta didik']);
    }

    private function resetForm(): void
    {
        $this->form = [
            'student_number' => '', 'name' => '', 'class_id' => '', 'major_id' => '',
            'gender' => '', 'email' => '', 'phone' => '', 'address' => '',
        ];
    }
}
