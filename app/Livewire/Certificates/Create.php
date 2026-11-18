<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Internship;
use App\Models\ScoreComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public array $selectedInternshipIds = [];

    public string $numberPrefix = 'SMKN1Krangkeng/TKJ/PKL';

    public string $numberStart = '1';

    public string $description = '';

    public string $search = '';

    public function mount(): void
    {
        Internship::syncStatusesFromDate();
    }

    public function updatedSearch(): void
    {
        $this->selectedInternshipIds = [];
    }

    public function selectAllEligible(): void
    {
        $this->selectedInternshipIds = $this->eligibleInternships()
            ->pluck('id')->map(fn ($id): string => (string) $id)->all();
    }

    public function clearSelection(): void
    {
        $this->selectedInternshipIds = [];
    }

    public function issue()
    {
        Internship::syncStatusesFromDate();
        $validated = $this->validate([
            'selectedInternshipIds' => ['required', 'array', 'min:1'],
            'selectedInternshipIds.*' => ['required', 'integer', 'distinct', 'exists:internships,id'],
            'numberPrefix' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN][\pL\pN.\/_-]*$/u'],
            'numberStart' => ['required', 'integer', 'min:1', 'max:999999'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'selectedInternshipIds.min' => 'Pilih minimal satu siswa yang akan menerima sertifikat.',
            'numberPrefix.regex' => 'Prefix hanya boleh berisi huruf, angka, titik, garis miring, garis bawah, dan tanda hubung.',
        ]);

        $eligibleIds = $this->eligibleInternships()->pluck('id')->map(fn ($id): string => (string) $id)->all();
        $invalidIds = array_diff(array_map('strval', $validated['selectedInternshipIds']), $eligibleIds);
        if ($invalidIds !== []) {
            $this->addError('selectedInternshipIds', 'Ada siswa yang belum selesai, belum lengkap nilainya, atau sudah memiliki sertifikat. Muat ulang pilihan lalu coba lagi.');

            return null;
        }
        if ((int) $validated['numberStart'] + count($validated['selectedInternshipIds']) - 1 > 999999) {
            $this->addError('numberStart', 'Rentang nomor sertifikat tidak boleh melebihi 999999.');

            return null;
        }

        $prefix = rtrim($validated['numberPrefix'], '/');
        $sequence = (int) $validated['numberStart'];
        $templateId = CertificateTemplate::query()->where('is_active', true)->value('id');
        $description = $validated['description'] ?: 'Telah menyelesaikan kegiatan Praktik Kerja Industri dengan baik.';
        $issued = DB::transaction(function () use ($validated, $eligibleIds, $prefix, &$sequence, $templateId, $description): array {
            $certificates = [];
            foreach ($validated['selectedInternshipIds'] as $internshipId) {
                $internship = Internship::query()->lockForUpdate()->with('scores.component')->findOrFail($internshipId);
                if (! in_array((string) $internship->id, $eligibleIds, true) || $internship->certificate()->exists()) {
                    throw new \RuntimeException('Pilihan sertifikat berubah saat sedang diproses.');
                }

                do {
                    $number = sprintf('%s/%03d', $prefix, $sequence++);
                } while (Certificate::query()->where('certificate_number', $number)->exists());

                $score = (float) $internship->score;
                $predicate = $score >= 90 ? 'Sangat Baik' : ($score >= 80 ? 'Baik' : 'Cukup');
                $certificates[] = $internship->certificate()->create([
                    'certificate_template_id' => $templateId,
                    'certificate_number' => $number,
                    'verification_code' => (string) Str::uuid(),
                    'issued_at' => today(),
                    'predicate' => $predicate,
                    'description' => $description,
                    'status' => 'issued',
                ]);
            }

            return $certificates;
        });

        session()->flash('success', count($issued).' sertifikat berhasil diterbitkan, nomor '.$issued[0]->certificate_number.' sampai '.$issued[array_key_last($issued)]->certificate_number.'.');

        return $this->redirectRoute('certificates.index');
    }

    public function render(): View
    {
        Internship::syncStatusesFromDate();

        return view('livewire.certificates.create', [
            'internships' => $this->eligibleInternships(),
            'selectedCount' => count($this->selectedInternshipIds),
        ])->layout('layouts.app', ['title' => 'Terbitkan sertifikat', 'breadcrumb' => 'Sertifikat']);
    }

    private function eligibleInternships()
    {
        $activeComponents = ScoreComponent::query()->where('is_active', true)->pluck('id');
        if ($activeComponents->isEmpty()) {
            return collect();
        }

        return Internship::query()
            ->where('status', 'completed')
            ->whereNotNull('score')
            ->whereDoesntHave('certificate')
            ->with(['student.schoolClass', 'company', 'scores.component', 'academicYear'])
            ->when(trim($this->search) !== '', function ($query): void {
                $term = trim($this->search);
                $query->where(fn ($scope) => $scope->whereHas('student', fn ($students) => $students->where('name', 'like', "%{$term}%")->orWhere('student_number', 'like', "%{$term}%"))
                    ->orWhereHas('company', fn ($companies) => $companies->where('name', 'like', "%{$term}%")));
            })
            ->orderByDesc('end_date')->get()
            ->filter(fn (Internship $internship): bool => $activeComponents->every(fn ($componentId): bool => $internship->scores->contains(fn ($score): bool => $score->score_component_id === $componentId && $score->score !== null)))
            ->values();
    }
}
