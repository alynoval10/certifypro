<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use App\Models\SchoolSetting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Show extends Component
{
    #[Locked]
    public Certificate $certificate;

    public string $revokeReason = '';

    public function mount(Certificate $certificate): void
    {
        $this->certificate = $certificate;
    }

    public function revoke(): void
    {
        if ($this->certificate->status === 'revoked') {
            $this->addError('revokeReason', 'Sertifikat ini sudah dicabut.');

            return;
        }
        $validated = $this->validate(['revokeReason' => ['required', 'string', 'max:1000']]);
        $this->certificate->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoke_reason' => $validated['revokeReason'],
        ]);
        session()->flash('success', 'Sertifikat berhasil dicabut.');
        $this->revokeReason = '';
    }

    public function render(): View
    {
        $certificate = Certificate::query()->with([
            'internship.student.schoolClass',
            'internship.student.majorRelation',
            'internship.company',
            'internship.academicYear',
            'internship.scores.component',
        ])->findOrFail($this->certificate->id);

        return view('livewire.certificates.show', [
            'certificate' => $certificate,
            'school' => SchoolSetting::query()->first(),
        ])->layout('layouts.certificate', ['title' => $certificate->certificate_number]);
    }
}
