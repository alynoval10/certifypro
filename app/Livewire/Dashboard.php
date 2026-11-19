<?php

namespace App\Livewire;

use App\Models\AcademicYear;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\Internship;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Dashboard extends Component
{
    #[Url(as: 'tahun', except: '')]
    public string $academicYearId = '';

    public function render(): View
    {
        Internship::syncStatusesFromDate();
        $activeAcademicYear = AcademicYear::active();
        $selectedAcademicYear = $this->academicYearId !== ''
            ? AcademicYear::query()->find((int) $this->academicYearId)
            : null;
        $selectedAcademicYear ??= $activeAcademicYear;
        $yearId = $selectedAcademicYear?->id;
        $internships = Internship::query()->where('academic_year_id', $yearId);
        $certificates = Certificate::query()->whereHas('internship', fn ($query) => $query->where('academic_year_id', $yearId));
        $internshipCount = (clone $internships)->count();
        $issuedCount = (clone $certificates)->where('status', 'issued')->count();

        return view('livewire.dashboard', [
            'activeAcademicYear' => $selectedAcademicYear,
            'academicYears' => AcademicYear::query()->orderByDesc('starts_on')->get(),
            'certificateCount' => (clone $certificates)->count(),
            'pendingCertificateCount' => (clone $internships)->where('status', 'completed')->whereDoesntHave('certificate')->count(),
            'studentCount' => Student::query()->whereHas('internships', fn ($query) => $query->where('academic_year_id', $yearId))->count(),
            'activeStudentCount' => Student::query()->where('status', 'active')->whereHas('internships', fn ($query) => $query->where('academic_year_id', $yearId))->count(),
            'graduatedStudentCount' => Student::query()->where('status', 'graduated')->whereHas('internships', fn ($query) => $query->where('academic_year_id', $yearId))->count(),
            'companyCount' => Company::query()->whereHas('internships', fn ($query) => $query->where('academic_year_id', $yearId))->count(),
            'ongoingCount' => (clone $internships)->where('status', 'ongoing')->count(),
            'completedCount' => (clone $internships)->where('status', 'completed')->count(),
            'school' => SchoolSetting::query()->first(),
            'issuedCount' => $issuedCount,
            'internshipCount' => $internshipCount,
            'progressPercentage' => $internshipCount > 0 ? (int) round($issuedCount / $internshipCount * 100) : 0,
            'recentCertificates' => (clone $certificates)->with(['internship.student', 'internship.company'])->latest('issued_at')->latest('id')->paginate(5),
        ])->layout('layouts.app', ['title' => 'Ringkasan', 'breadcrumb' => 'Ringkasan']);
    }
}
