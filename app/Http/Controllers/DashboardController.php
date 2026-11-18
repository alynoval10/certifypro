<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\Internship;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $activeAcademicYear = AcademicYear::active();
        $yearId = $activeAcademicYear?->id;
        $internships = Internship::query()->where('academic_year_id', $yearId);
        $certificates = Certificate::query()->whereHas('internship', fn ($query) => $query->where('academic_year_id', $yearId));
        $internshipCount = (clone $internships)->count();
        $issuedCount = (clone $certificates)->where('status', 'issued')->count();

        return view('dashboard.index', [
            'activeAcademicYear' => $activeAcademicYear,
            'certificateCount' => (clone $certificates)->count(),
            'pendingCertificateCount' => (clone $internships)
                ->where('status', 'completed')
                ->whereDoesntHave('certificate')
                ->count(),
            'studentCount' => Student::query()->whereHas('internships', fn ($query) => $query->where('academic_year_id', $yearId))->count(),
            'companyCount' => Company::query()->whereHas('internships', fn ($query) => $query->where('academic_year_id', $yearId))->count(),
            'ongoingCount' => (clone $internships)->where('status', 'ongoing')->count(),
            'completedCount' => (clone $internships)->where('status', 'completed')->count(),
            'school' => SchoolSetting::query()->first(),
            'issuedCount' => $issuedCount,
            'internshipCount' => $internshipCount,
            'progressPercentage' => $internshipCount > 0 ? (int) round($issuedCount / $internshipCount * 100) : 0,
            'recentCertificates' => (clone $certificates)
                ->with(['internship.student', 'internship.company'])
                ->latest('issued_at')
                ->latest('id')
                ->paginate(5),
        ]);
    }
}
