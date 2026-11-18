<?php

namespace App\Livewire\Assessments;

use App\Models\AcademicYear;
use App\Models\Internship;
use App\Models\InternshipScore;
use App\Models\ScoreComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?int $academicYearId = null;

    public string $search = '';

    public string $componentName = '';

    public string $componentMax = '100';

    public string $componentWeight = '10';

    /** @var array<int, array{name: string, max_score: string, weight: string}> */
    public array $componentSettings = [];

    /** @var array<int, array<int, string|float|null>> */
    public array $scores = [];

    public function mount(): void
    {
        $this->academicYearId = AcademicYear::active()?->id;
        $this->loadScores();
        $this->loadComponentSettings();
    }

    public function updatedAcademicYearId(): void
    {
        $this->resetPage();
        $this->loadScores();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function addComponent(): void
    {
        $validated = $this->validate([
            'componentName' => ['required', 'string', 'max:120', 'unique:score_components,name'],
            'componentMax' => ['required', 'numeric', 'gt:0', 'max:999.99'],
            'componentWeight' => ['required', 'numeric', 'gt:0', 'max:999.99'],
        ]);
        ScoreComponent::query()->create([
            'name' => trim($validated['componentName']),
            'max_score' => $validated['componentMax'],
            'weight' => $validated['componentWeight'],
            'sort_order' => (int) ScoreComponent::query()->max('sort_order') + 1,
            'is_active' => true,
        ]);
        $this->reset('componentName');
        $this->loadComponentSettings();
        session()->flash('success', 'Komponen nilai berhasil ditambahkan.');
    }

    public function saveComponentSettings(int $componentId): void
    {
        $component = ScoreComponent::query()->findOrFail($componentId);
        if ($component->internshipScores()->exists()) {
            session()->flash('error', 'Aspek yang sudah memiliki nilai siswa tidak bisa diubah agar riwayat sertifikat tetap konsisten.');

            return;
        }
        $field = 'componentSettings.'.$componentId;
        $validated = $this->validate([
            $field.'.name' => ['required', 'string', 'max:120', Rule::unique('score_components', 'name')->ignore($component)],
            $field.'.max_score' => ['required', 'numeric', 'gt:0', 'max:999.99'],
            $field.'.weight' => ['required', 'numeric', 'gt:0', 'max:999.99'],
        ]);
        $component->update($validated['componentSettings'][$componentId]);
        session()->flash('success', 'Pengaturan aspek penilaian berhasil diperbarui.');
    }

    public function toggleComponent(int $componentId): void
    {
        $component = ScoreComponent::query()->findOrFail($componentId);
        $component->update(['is_active' => ! $component->is_active]);
        session()->flash('success', $component->is_active ? 'Komponen nilai diaktifkan.' : 'Komponen nilai diarsipkan.');
    }

    public function deleteComponent(int $componentId): void
    {
        $component = ScoreComponent::query()->findOrFail($componentId);
        if ($component->internshipScores()->exists()) {
            session()->flash('error', 'Komponen ini sudah dipakai pada nilai siswa. Arsipkan jika tidak ingin digunakan lagi.');

            return;
        }
        $component->delete();
        session()->flash('success', 'Komponen nilai dihapus.');
    }

    public function saveScores(): void
    {
        $validated = $this->validate([
            'academicYearId' => ['required', 'integer', 'exists:academic_years,id'],
            'scores' => ['array'],
            'scores.*' => ['array'],
            'scores.*.*' => ['nullable', 'numeric', 'min:0'],
        ]);
        $components = ScoreComponent::query()->get()->keyBy('id');
        foreach ($validated['scores'] as $internshipId => $componentsScores) {
            foreach ($componentsScores as $componentId => $score) {
                if ($score === null || $score === '') {
                    continue;
                }
                $component = $components->get((int) $componentId);
                if (! $component || (float) $score > (float) $component->max_score) {
                    throw ValidationException::withMessages([
                        'scores.'.$internshipId.'.'.$componentId => 'Nilai harus berada di antara 0 dan nilai maksimum komponen.',
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validated, $components): void {
            foreach ($validated['scores'] as $internshipId => $componentsScores) {
                $internship = Internship::query()
                    ->where('academic_year_id', $validated['academicYearId'])
                    ->find($internshipId);
                if (! $internship) {
                    continue;
                }
                foreach ($componentsScores as $componentId => $score) {
                    $component = $components->get((int) $componentId);
                    if (! $component || ($score === null || $score === '') && ! $component->internshipScores()->where('internship_id', $internship->id)->exists()) {
                        continue;
                    }
                    if ($score === null || $score === '') {
                        InternshipScore::query()->where('internship_id', $internship->id)->where('score_component_id', $component->id)->delete();
                    } else {
                        InternshipScore::query()->updateOrCreate(
                            ['internship_id' => $internship->id, 'score_component_id' => $component->id],
                            ['score' => $score],
                        );
                    }
                }
                $internship->recalculateFinalScore();
            }
        });

        session()->flash('success', 'Nilai siswa berhasil disimpan dan nilai akhir dihitung ulang.');
        $this->loadScores();
    }

    public function render(): View
    {
        $components = ScoreComponent::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $internships = Internship::query()
            ->with(['student.schoolClass', 'company', 'scores'])
            ->when($this->academicYearId, fn ($query) => $query->where('academic_year_id', $this->academicYearId))
            ->when(trim($this->search) !== '', function ($query): void {
                $search = trim($this->search);
                $query->whereHas('student', fn ($students) => $students->where('name', 'like', "%{$search}%")->orWhere('student_number', 'like', "%{$search}%"));
            })
            ->orderBy('start_date')
            ->paginate(25);

        return view('livewire.assessments.index', [
            'academicYears' => AcademicYear::query()->orderByDesc('starts_on')->get(),
            'components' => $components,
            'allComponents' => ScoreComponent::query()->withCount('internshipScores')->orderBy('sort_order')->get(),
            'internships' => $internships,
        ])->layout('layouts.app', ['title' => 'Penilaian siswa', 'breadcrumb' => 'Penilaian siswa']);
    }

    private function loadScores(): void
    {
        if (! $this->academicYearId) {
            $this->scores = [];

            return;
        }

        $this->scores = Internship::query()
            ->where('academic_year_id', $this->academicYearId)
            ->with('scores')
            ->get()
            ->mapWithKeys(fn (Internship $internship) => [
                $internship->id => $internship->scores->mapWithKeys(fn (InternshipScore $score) => [$score->score_component_id => $score->score === null ? null : (string) $score->score])->all(),
            ])
            ->all();
    }

    private function loadComponentSettings(): void
    {
        $this->componentSettings = ScoreComponent::query()->get()->mapWithKeys(fn (ScoreComponent $component) => [
            $component->id => [
                'name' => $component->name,
                'max_score' => (string) $component->max_score,
                'weight' => (string) $component->weight,
            ],
        ])->all();
    }
}
