<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Company;
use App\Models\Internship;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternshipController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();

        $internships = Internship::query()
            ->with(['student', 'company', 'certificate'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('student', function ($studentQuery) use ($search): void {
                    $studentQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                })->orWhereHas('company', function ($companyQuery) use ($search): void {
                    $companyQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['ongoing', 'completed'], true), fn ($query) => $query->where('status', $status))
            ->latest('start_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('internships.index', compact('internships', 'search', 'status'));
    }

    public function create(): View|RedirectResponse
    {
        if (Student::query()->doesntExist() || Company::query()->doesntExist()) {
            return redirect()->route('internships.index')->with('error', 'Tambahkan minimal satu siswa dan satu mitra sebelum membuat data Prakerin.');
        }

        return view('internships.form', [
            'internship' => new Internship,
            'students' => Student::query()->orderBy('name')->get(),
            'companies' => Company::query()->orderBy('name')->get(),
            'academicYears' => AcademicYear::query()->orderByDesc('starts_on')->get(),
            'activeAcademicYear' => AcademicYear::active(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        Internship::create($validated);

        return redirect()->route('internships.index')->with('success', 'Data Prakerin berhasil ditambahkan.');
    }

    public function edit(Internship $internship): View|RedirectResponse
    {
        if ($internship->certificate()->exists()) {
            return redirect()->route('internships.index')->with('error', 'Data Prakerin yang sudah memiliki sertifikat tidak dapat diubah.');
        }

        return view('internships.form', [
            'internship' => $internship,
            'students' => Student::query()->orderBy('name')->get(),
            'companies' => Company::query()->orderBy('name')->get(),
            'academicYears' => AcademicYear::query()->orderByDesc('starts_on')->get(),
            'activeAcademicYear' => AcademicYear::active(),
        ]);
    }

    public function update(Request $request, Internship $internship): RedirectResponse
    {
        if ($internship->certificate()->exists()) {
            return redirect()->route('internships.index')->with('error', 'Data Prakerin yang sudah memiliki sertifikat tidak dapat diubah.');
        }

        $internship->update($this->validatedData($request));

        return redirect()->route('internships.index')->with('success', 'Data Prakerin berhasil diperbarui.');
    }

    public function destroy(Internship $internship): RedirectResponse
    {
        if ($internship->certificate()->exists()) {
            return back()->with('error', 'Prakerin yang memiliki sertifikat tidak dapat dihapus.');
        }

        $internship->delete();

        return redirect()->route('internships.index')->with('success', 'Data Prakerin berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'department' => ['nullable', 'string', 'max:120'],
            'mentor_name' => ['nullable', 'string', 'max:255'],
            'school_supervisor' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'final_note' => ['nullable', 'string', 'max:4000'],
            'status' => ['required', Rule::in(['ongoing', 'completed'])],
        ]);

        $validated['academic_year'] = AcademicYear::query()->findOrFail($validated['academic_year_id'])->name;

        return $validated;
    }
}
