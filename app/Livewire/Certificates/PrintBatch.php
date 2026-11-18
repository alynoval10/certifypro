<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\SchoolSetting;
use App\Services\CertificateQrCode;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PrintBatch extends Component
{
    /** @var list<int> */
    public array $certificateIds = [];

    public function mount(): void
    {
        $this->certificateIds = collect(explode(',', (string) request()->query('ids', '')))
            ->filter(fn (string $id): bool => ctype_digit($id))
            ->map(fn (string $id): int => (int) $id)
            ->unique()->values()->all();
        abort_if($this->certificateIds === [] || count($this->certificateIds) > 200, 404);
    }

    public function render(): View
    {
        $certificates = Certificate::query()->with([
            'template', 'internship.student.schoolClass', 'internship.student.majorRelation',
            'internship.company', 'internship.academicYear', 'internship.scores.component',
        ])->whereIn('id', $this->certificateIds)->where('status', 'issued')->get()->sortBy(
            fn (Certificate $certificate): int => array_search($certificate->id, $this->certificateIds, true) ?: 0,
        )->values();
        abort_if($certificates->count() !== count($this->certificateIds), 404);
        $qrCodes = $certificates->mapWithKeys(fn (Certificate $certificate): array => [
            $certificate->id => app(CertificateQrCode::class)->dataUri($certificate),
        ]);

        return view('livewire.certificates.print-batch', [
            'certificates' => $certificates,
            'qrCodes' => $qrCodes,
            'school' => SchoolSetting::query()->first(),
            'activeTemplate' => CertificateTemplate::query()->where('is_active', true)->first(),
        ])->layout('layouts.certificate', ['title' => 'Cetak banyak sertifikat']);
    }
}
