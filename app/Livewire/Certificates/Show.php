<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\SchoolSetting;
use App\Services\CertificateQrCode;
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
            'template',
        ])->findOrFail($this->certificate->id);

        return view('livewire.certificates.show', [
            'certificate' => $certificate,
            'school' => SchoolSetting::query()->first(),
            'template' => $certificate->template ?? CertificateTemplate::query()->where('is_active', true)->first(),
            'qrCode' => app(CertificateQrCode::class)->dataUri($certificate),
        ])->layout('layouts.certificate', ['title' => $certificate->certificate_number]);
    }
}
