<?php

namespace App\Livewire\Notifications;

use App\Models\AcademicYear;
use App\Models\Internship;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dropdown extends Component
{
    public function render(): View
    {
        $yearId = AcademicYear::active()?->id;
        $yearScope = fn ($query) => $query->when($yearId, fn ($internships) => $internships->where('academic_year_id', $yearId));
        $soonEndingCount = Internship::query()->where($yearScope)
            ->where('status', 'ongoing')
            ->whereDate('end_date', '>=', today())
            ->whereDate('end_date', '<=', today()->addDays(14))
            ->count();
        $pendingCertificateCount = Internship::query()->where($yearScope)
            ->where(fn ($query) => $query->where('status', 'completed')->orWhereDate('end_date', '<', today()))
            ->whereDoesntHave('certificate')
            ->count();
        $incompleteScoreCount = Internship::query()->where($yearScope)
            ->whereDate('end_date', '<', today())
            ->whereNull('score')
            ->count();

        $notifications = collect([
            ['title' => 'Prakerin segera berakhir', 'description' => 'Periksa penilaian dan kelengkapan siswa.', 'count' => $soonEndingCount, 'route' => 'internships.index', 'tone' => 'amber'],
            ['title' => 'Nilai akhir belum tersedia', 'description' => 'Prakerin berakhir tetapi nilai belum dihitung.', 'count' => $incompleteScoreCount, 'route' => 'assessments.index', 'tone' => 'sky'],
            ['title' => 'Sertifikat siap diterbitkan', 'description' => 'Prakerin selesai tanpa sertifikat.', 'count' => $pendingCertificateCount, 'route' => 'certificates.create', 'tone' => 'emerald'],
        ])->filter(fn (array $item): bool => $item['count'] > 0)->values();

        return view('livewire.notifications.dropdown', [
            'notifications' => $notifications,
            'notificationCount' => $notifications->sum('count'),
            'academicYear' => AcademicYear::active()?->name,
        ]);
    }
}
