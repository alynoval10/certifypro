<?php

namespace App\Livewire\Internships;

use App\Models\AcademicYear;
use App\Models\Company;
use App\Models\Internship;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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

    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public array $batch = [];

    public array $selectedStudentIds = [];

    public string $availableStudentSearch = '';

    public function mount(?Internship $internship = null): void
    {
        $this->resetForm();
        if (request()->routeIs('internships.create')) {
            $this->create();
        } elseif ($internship?->exists) {
            $this->edit($internship->id);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBatch(mixed $value, ?string $key = null): void
    {
        if ($key === 'company_id') {
            $company = Company::query()->find($value);
            $this->batch['mentor_name'] = $company?->mentor_name ?? '';
            $this->batch['department'] = $company?->industry ?? '';
        }
        if (in_array($key, ['company_id', 'academic_year_id'], true)) {
            $this->selectedStudentIds = [];
        }
    }

    public function addStudentToSelection(int $studentId): void
    {
        $yearId = (int) ($this->batch['academic_year_id'] ?? 0);
        $eligible = Student::query()
            ->whereKey($studentId)
            ->whereDoesntHave('internships', fn (Builder $query) => $query->where('academic_year_id', $yearId))
            ->exists();
        if ($yearId > 0 && $eligible && ! in_array($studentId, $this->selectedStudentIds, true)) {
            $this->selectedStudentIds[] = $studentId;
        }
    }

    public function removeStudentFromSelection(int $studentId): void
    {
        $this->selectedStudentIds = array_values(array_filter(
            $this->selectedStudentIds,
            fn (int $selectedId): bool => $selectedId !== $studentId,
        ));
    }

    public function saveBatchPlacement(): void
    {
        $validated = $this->validate([
            'batch.company_id' => ['required', 'integer', 'exists:companies,id'],
            'batch.academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'batch.start_date' => ['required', 'date', 'before_or_equal:batch.end_date'],
            'batch.end_date' => ['required', 'date', 'after_or_equal:batch.start_date'],
            'batch.department' => ['nullable', 'string', 'max:120'],
            'batch.school_supervisor_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'selectedStudentIds' => ['required', 'array', 'min:1'],
            'selectedStudentIds.*' => ['required', 'integer', 'distinct', 'exists:students,id'],
        ]);
        $company = Company::query()->findOrFail($validated['batch']['company_id']);
        $academicYear = AcademicYear::query()->findOrFail($validated['batch']['academic_year_id']);
        $teacher = Teacher::query()->find($validated['batch']['school_supervisor_id'] ?? null);
        $createdCount = 0;
        DB::transaction(function () use ($validated, $company, $academicYear, $teacher, &$createdCount): void {
            foreach ($validated['selectedStudentIds'] as $studentId) {
                if (Internship::query()->where('student_id', $studentId)->where('academic_year_id', $academicYear->id)->exists()) {
                    continue;
                }
                Internship::query()->create([
                    'student_id' => $studentId,
                    'company_id' => $company->id,
                    'academic_year_id' => $academicYear->id,
                    'academic_year' => $academicYear->name,
                    'department' => $validated['batch']['department'] ?? $company->industry,
                    'mentor_name' => $company->mentor_name,
                    'school_supervisor' => $teacher?->name,
                    'school_supervisor_id' => $teacher?->id,
                    'start_date' => $validated['batch']['start_date'],
                    'end_date' => $validated['batch']['end_date'],
                    'status' => 'ongoing',
                ]);
                $createdCount++;
            }
        });

        $this->selectedStudentIds = [];
        session()->flash('success', $createdCount.' siswa berhasil ditempatkan di '.$company->name.'.');
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $internshipId): void
    {
        $internship = Internship::query()->findOrFail($internshipId);
        if ($internship->certificate()->exists()) {
            session()->flash('error', 'Data Prakerin yang sudah memiliki sertifikat tidak dapat diubah.');

            return;
        }
        $this->editingId = $internship->id;
        $this->form = $internship->only(['student_id', 'company_id', 'academic_year_id', 'department', 'mentor_name', 'school_supervisor', 'school_supervisor_id', 'start_date', 'end_date', 'final_note', 'status']);
        $this->form['start_date'] = $internship->start_date?->format('Y-m-d');
        $this->form['end_date'] = $internship->end_date?->format('Y-m-d');
        $this->showForm = true;
    }

    public function save(): void
    {
        $internship = $this->editingId ? Internship::query()->findOrFail($this->editingId) : new Internship;
        if ($internship->exists && $internship->certificate()->exists()) {
            session()->flash('error', 'Data Prakerin yang sudah memiliki sertifikat tidak dapat diubah.');
            $this->showForm = false;

            return;
        }
        $data = $this->validate([
            'form.student_id' => ['required', 'integer', 'exists:students,id'],
            'form.company_id' => ['required', 'integer', 'exists:companies,id'],
            'form.academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'form.department' => ['nullable', 'string', 'max:120'],
            'form.mentor_name' => ['nullable', 'string', 'max:255'],
            'form.school_supervisor_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'form.start_date' => ['required', 'date', 'before_or_equal:form.end_date'],
            'form.end_date' => ['required', 'date', 'after_or_equal:form.start_date'],
            'form.final_note' => ['nullable', 'string', 'max:4000'],
            'form.status' => ['required', Rule::in(['ongoing', 'completed'])],
        ])['form'];
        $data['academic_year'] = AcademicYear::query()->findOrFail($data['academic_year_id'])->name;
        $teacher = Teacher::query()->find($data['school_supervisor_id'] ?? null);
        $data['school_supervisor'] = $teacher?->name ?? ($internship->exists ? $internship->school_supervisor : null);
        $internship->fill($data)->save();
        $this->showForm = false;
        $this->resetForm();
        session()->flash('success', 'Data Prakerin berhasil disimpan.');

        $this->redirectRoute('internships.index');
    }

    public function delete(int $internshipId): void
    {
        $internship = Internship::query()->findOrFail($internshipId);
        if ($internship->certificate()->exists()) {
            session()->flash('error', 'Prakerin yang memiliki sertifikat tidak dapat dihapus.');

            return;
        }
        $internship->delete();
        session()->flash('success', 'Data Prakerin berhasil dihapus.');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
        if (request()->routeIs('internships.create', 'internships.edit')) {
            $this->redirectRoute('internships.index');

            return;
        }
    }

    public function render(): View
    {
        $search = trim($this->search);

        return view('livewire.internships.index', [
            'internships' => Internship::query()->with(['student', 'company', 'certificate', 'academicYear', 'schoolSupervisor'])
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $scope) use ($search): void {
                        $scope->whereHas('student', fn (Builder $students) => $students->where('name', 'like', "%{$search}%")->orWhere('student_number', 'like', "%{$search}%"))
                            ->orWhereHas('company', fn (Builder $companies) => $companies->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when(in_array($this->statusFilter, ['ongoing', 'completed'], true), fn (Builder $query) => $query->where('status', $this->statusFilter))
                ->latest('start_date')->latest('id')->paginate(12),
            'students' => Student::query()->orderBy('name')->get(),
            'companies' => Company::query()->orderBy('name')->get(),
            'academicYears' => AcademicYear::query()->orderByDesc('starts_on')->get(),
            'teachers' => Teacher::query()->orderBy('name')->get(),
            'activeAcademicYear' => AcademicYear::active(),
            'availableStudents' => Student::query()->whereDoesntHave('internships', fn (Builder $query) => $query->where('academic_year_id', $this->batch['academic_year_id'] ?? AcademicYear::active()?->id))
                ->when(trim($this->availableStudentSearch) !== '', function (Builder $query): void {
                    $search = trim($this->availableStudentSearch);
                    $query->where(fn (Builder $students) => $students->where('name', 'like', "%{$search}%")->orWhere('student_number', 'like', "%{$search}%"));
                })->orderBy('name')->limit(300)->get(),
            'selectedStudents' => Student::query()->whereIn('id', $this->selectedStudentIds)->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Data Prakerin', 'breadcrumb' => 'Data Prakerin']);
    }

    private function resetForm(): void
    {
        $this->form = [
            'student_id' => '', 'company_id' => '', 'academic_year_id' => AcademicYear::active()?->id ?? '',
            'department' => '', 'mentor_name' => '', 'school_supervisor_id' => '', 'start_date' => '',
            'end_date' => '', 'final_note' => '', 'status' => 'ongoing',
        ];
        $this->batch = [
            'company_id' => '',
            'academic_year_id' => AcademicYear::active()?->id ?? '',
            'department' => '',
            'mentor_name' => '',
            'school_supervisor_id' => '',
            'start_date' => '',
            'end_date' => '',
        ];
    }
}
