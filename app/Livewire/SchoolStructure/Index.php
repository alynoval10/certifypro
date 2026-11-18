<?php

namespace App\Livewire\SchoolStructure;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public string $className = '';

    public string $majorName = '';

    public string $yearStartsOn = '';

    public string $yearEndsOn = '';

    public function mount(): void
    {
        $active = AcademicYear::active();
        $this->yearStartsOn = $active?->starts_on?->copy()->addYear()->format('Y-m-d') ?? '';
        $this->yearEndsOn = $active?->ends_on?->copy()->addYear()->format('Y-m-d') ?? '';
    }

    public function addClass(): void
    {
        $validated = $this->validate(['className' => ['required', 'string', 'max:50', 'unique:school_classes,name']]);
        SchoolClass::query()->create(['name' => trim($validated['className'])]);
        $this->reset('className');
        session()->flash('success', 'Kelas berhasil ditambahkan.');
    }

    public function deleteClass(int $id): void
    {
        $class = SchoolClass::query()->findOrFail($id);
        if ($class->students()->exists()) {
            session()->flash('error', 'Kelas masih dipakai peserta didik dan tidak dapat dihapus.');

            return;
        }
        $class->delete();
        session()->flash('success', 'Kelas berhasil dihapus.');
    }

    public function addMajor(): void
    {
        $validated = $this->validate(['majorName' => ['required', 'string', 'max:120', 'unique:majors,name']]);
        Major::query()->create(['name' => trim($validated['majorName'])]);
        $this->reset('majorName');
        session()->flash('success', 'Jurusan berhasil ditambahkan.');
    }

    public function deleteMajor(int $id): void
    {
        $major = Major::query()->findOrFail($id);
        if ($major->students()->exists()) {
            session()->flash('error', 'Jurusan masih dipakai peserta didik dan tidak dapat dihapus.');

            return;
        }
        $major->delete();
        session()->flash('success', 'Jurusan berhasil dihapus.');
    }

    public function addAcademicYear(): void
    {
        $validated = $this->validate([
            'yearStartsOn' => ['required', 'date'],
            'yearEndsOn' => ['required', 'date', 'after:yearStartsOn'],
        ]);
        $name = date('Y', strtotime($validated['yearStartsOn'])).'/'.date('Y', strtotime($validated['yearEndsOn']));
        if (AcademicYear::query()->where('name', $name)->exists()) {
            $this->addError('yearStartsOn', 'Tahun ajaran '.$name.' sudah terdaftar.');

            return;
        }
        AcademicYear::query()->create([
            'name' => $name,
            'starts_on' => $validated['yearStartsOn'],
            'ends_on' => $validated['yearEndsOn'],
            'is_active' => false,
        ]);
        $this->yearStartsOn = now()->addYear()->month(7)->day(1)->format('Y-m-d');
        $this->yearEndsOn = now()->addYears(2)->month(6)->day(30)->format('Y-m-d');
        session()->flash('success', 'Tahun ajaran '.$name.' berhasil ditambahkan.');
    }

    public function activateAcademicYear(int $id): void
    {
        $year = AcademicYear::query()->findOrFail($id);
        DB::transaction(function () use ($year): void {
            AcademicYear::query()->where('is_active', true)->update(['is_active' => false]);
            $year->update(['is_active' => true]);
        });
        session()->flash('success', 'Tahun ajaran '.$year->name.' sekarang aktif.');
    }

    public function deleteAcademicYear(int $id): void
    {
        $year = AcademicYear::query()->findOrFail($id);
        if ($year->is_active || $year->internships()->exists()) {
            session()->flash('error', $year->is_active ? 'Tahun ajaran aktif tidak dapat dihapus.' : 'Tahun ajaran dengan riwayat Prakerin tidak dapat dihapus.');

            return;
        }
        $year->delete();
        session()->flash('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.school-structure.index', [
            'classes' => SchoolClass::query()->withCount('students')->orderBy('name')->get(),
            'majors' => Major::query()->withCount('students')->orderBy('name')->get(),
            'academicYears' => AcademicYear::query()->withCount('internships')->orderByDesc('starts_on')->get(),
        ])->layout('layouts.app', ['title' => 'Data akademik', 'breadcrumb' => 'Data akademik']);
    }
}
