<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolStructureController extends Controller
{
    public function index(): View
    {
        return view('school-structure.index', [
            'classes' => SchoolClass::query()->withCount('students')->orderBy('name')->get(),
            'majors' => Major::query()->withCount('students')->orderBy('name')->get(),
            'academicYears' => AcademicYear::query()->withCount('internships')->orderByDesc('starts_on')->get(),
        ]);
    }

    public function storeClass(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:50', 'unique:school_classes,name']]);
        SchoolClass::query()->create($validated);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function destroyClass(SchoolClass $schoolClass): RedirectResponse
    {
        if ($schoolClass->students()->exists()) {
            return back()->with('error', 'Kelas masih dipakai peserta didik, sehingga tidak dapat dihapus.');
        }
        $schoolClass->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function storeMajor(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:120', 'unique:majors,name']]);
        Major::query()->create($validated);

        return back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function destroyMajor(Major $major): RedirectResponse
    {
        if ($major->students()->exists()) {
            return back()->with('error', 'Jurusan masih dipakai peserta didik, sehingga tidak dapat dihapus.');
        }
        $major->delete();

        return back()->with('success', 'Jurusan berhasil dihapus.');
    }

    public function storeAcademicYear(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after:starts_on'],
        ]);
        $startYear = (int) date('Y', strtotime($validated['starts_on']));
        $endYear = (int) date('Y', strtotime($validated['ends_on']));
        $name = $startYear.'/'.$endYear;
        if (AcademicYear::query()->where('name', $name)->exists()) {
            return back()->withErrors(['starts_on' => 'Tahun ajaran '.$name.' sudah terdaftar.']);
        }

        $year = AcademicYear::query()->create([
            'name' => $name,
            'starts_on' => $validated['starts_on'],
            'ends_on' => $validated['ends_on'],
            'is_active' => false,
        ]);

        return back()->with('success', 'Tahun ajaran '.$year->name.' berhasil ditambahkan.');
    }

    public function activateAcademicYear(AcademicYear $academicYear): RedirectResponse
    {
        DB::transaction(function () use ($academicYear): void {
            AcademicYear::query()->where('is_active', true)->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);
        });

        return back()->with('success', 'Tahun ajaran '.$academicYear->name.' sekarang aktif.');
    }

    public function destroyAcademicYear(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Tahun ajaran aktif tidak dapat dihapus. Aktifkan tahun lain terlebih dahulu.');
        }
        if ($academicYear->internships()->exists()) {
            return back()->with('error', 'Tahun ajaran ini memiliki riwayat Prakerin, sehingga tidak dapat dihapus.');
        }
        $academicYear->delete();

        return back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
