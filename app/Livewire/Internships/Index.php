<?php

namespace App\Livewire\Internships;

use App\Models\AcademicYear;
use App\Models\Company;
use App\Models\Internship;
use App\Models\Student;
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

    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

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
        $this->form = $internship->only(['student_id', 'company_id', 'academic_year_id', 'department', 'mentor_name', 'school_supervisor', 'start_date', 'end_date', 'final_note', 'status']);
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
            'form.school_supervisor' => ['nullable', 'string', 'max:255'],
            'form.start_date' => ['required', 'date', 'before_or_equal:form.end_date'],
            'form.end_date' => ['required', 'date', 'after_or_equal:form.start_date'],
            'form.final_note' => ['nullable', 'string', 'max:4000'],
            'form.status' => ['required', Rule::in(['ongoing', 'completed'])],
        ])['form'];
        $data['academic_year'] = AcademicYear::query()->findOrFail($data['academic_year_id'])->name;
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
            'internships' => Internship::query()->with(['student', 'company', 'certificate', 'academicYear'])
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
            'activeAcademicYear' => AcademicYear::active(),
        ])->layout('layouts.app', ['title' => 'Data Prakerin', 'breadcrumb' => 'Data Prakerin']);
    }

    private function resetForm(): void
    {
        $this->form = [
            'student_id' => '', 'company_id' => '', 'academic_year_id' => AcademicYear::active()?->id ?? '',
            'department' => '', 'mentor_name' => '', 'school_supervisor' => '', 'start_date' => '',
            'end_date' => '', 'final_note' => '', 'status' => 'ongoing',
        ];
    }
}
