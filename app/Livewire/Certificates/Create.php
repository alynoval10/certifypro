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
    public string $internshipId = '';

    public string $description = '';

    public function mount(): void
    {
        $this->internshipId = (string) (Internship::query()->where('status', 'completed')->whereNotNull('score')->whereDoesntHave('certificate')->orderByDesc('end_date')->value('id') ?? '');
    }

    public function issue()
    {
        $validated = $this->validate([
            'internshipId' => ['required', 'integer', 'exists:internships,id'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
        $certificate = DB::transaction(function () use ($validated): ?Certificate {
            $internship = Internship::query()->lockForUpdate()->with('scores.component')->findOrFail($validated['internshipId']);
            if ($internship->status !== 'completed' || $internship->certificate()->exists()) {
                $this->addError('internshipId', 'Pilih catatan Prakerin selesai yang belum memiliki sertifikat.');

                return null;
            }

            $requiredScores = ScoreComponent::query()->where('is_active', true)->count();
            $completedScores = $internship->scores->filter(fn ($score) => $score->score !== null && $score->component?->is_active)->count();
            if ($internship->score === null || $requiredScores === 0 || $completedScores < $requiredScores) {
                $this->addError('internshipId', 'Lengkapi seluruh komponen nilai aktif sebelum menerbitkan sertifikat.');

                return null;
            }

            $score = (float) $internship->score;
            $predicate = $score >= 90 ? 'Sangat Baik' : ($score >= 80 ? 'Baik' : 'Cukup');
            $certificate = $internship->certificate()->create([
                'verification_code' => (string) Str::uuid(),
                'certificate_template_id' => CertificateTemplate::query()->where('is_active', true)->value('id'),
                'issued_at' => today(),
                'predicate' => $predicate,
                'description' => $validated['description'] ?: 'Telah menyelesaikan kegiatan Praktik Kerja Industri dengan baik.',
                'status' => 'issued',
            ]);
            $certificate->update(['certificate_number' => sprintf('PRK-%s-%05d', now()->format('Y'), $certificate->id)]);

            return $certificate;
        });

        if ($certificate) {
            return $this->redirect(route('certificates.show', $certificate));
        }
    }

    public function render(): View
    {
        $eligibleInternships = Internship::query()->where('status', 'completed')->whereNotNull('score')->whereDoesntHave('certificate')
            ->with(['student', 'company', 'scores.component', 'academicYear'])->orderByDesc('end_date')->get();

        return view('livewire.certificates.create', [
            'internships' => $eligibleInternships,
            'selectedInternship' => $eligibleInternships->firstWhere('id', (int) $this->internshipId),
        ])->layout('layouts.app', ['title' => 'Terbitkan sertifikat', 'breadcrumb' => 'Sertifikat']);
    }
}
