<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $students = Student::query()
            ->withCount('internships')
            ->with(['schoolClass', 'majorRelation'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function (Builder $studentQuery) use ($search): void {
                    $studentQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%")
                        ->orWhere('class_name', 'like', "%{$search}%")
                        ->orWhereHas('schoolClass', fn (Builder $classQuery) => $classQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('students.index', compact('students', 'search'));
    }

    public function create(): View
    {
        return view('students.form', [
            'student' => new Student,
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'majors' => Major::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:30', 'unique:students,student_number'],
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'major_id' => ['required', 'integer', 'exists:majors,id'],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);

        $student = new Student($validated);
        $this->syncLabels($student);
        $student->save();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student): View
    {
        return view('students.form', [
            'student' => $student,
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'majors' => Major::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:30', Rule::unique('students', 'student_number')->ignore($student)],
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'major_id' => ['required', 'integer', 'exists:majors,id'],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);

        $student->fill($validated);
        $this->syncLabels($student);
        $student->save();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->internships()->exists()) {
            return back()->with('error', 'Siswa memiliki riwayat Prakerin dan tidak dapat dihapus.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function downloadTemplate(): Response
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Peserta Didik');
        $sheet->fromArray(['NIS / NISN', 'Nama', 'Kelas', 'Jurusan', 'Jenis Kelamin (L/P)', 'Email', 'Telepon', 'Alamat'], null, 'A1');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:H1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF4F46E5');
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:H1');
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('G:G')->getNumberFormat()->setFormatCode('@');
        $genderValidation = $sheet->getCell('E2')->getDataValidation();
        $genderValidation->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setAllowBlank(true)
            ->setShowDropDown(false)
            ->setFormula1('"L,P"');
        for ($row = 3; $row <= 2001; $row++) {
            $sheet->getCell('E'.$row)->setDataValidation(clone $genderValidation);
        }
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $instructions = $spreadsheet->createSheet();
        $instructions->setTitle('Petunjuk');
        $instructions->fromArray([
            ['Panduan impor peserta didik'],
            ['Isi data pada lembar Peserta Didik. Jangan ubah nama kolom pada baris pertama.'],
            ['Kolom wajib: NIS / NISN, Nama, Kelas, dan Jurusan.'],
            ['Kolom opsional: Jenis Kelamin (L/P), Email, Telepon, dan Alamat.'],
            ['NIS/NISN tidak boleh duplikat. Baris yang tidak valid akan dilewati dan dilaporkan.'],
            ['Kelas dan jurusan baru pada file akan otomatis ditambahkan ke data master.'],
            ['File dibatasi maksimal 2.000 baris data dan harus disimpan sebagai .xlsx.'],
        ]);
        $instructions->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructions->getColumnDimension('A')->setWidth(105);
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = (string) ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template-peserta-didik.xlsx"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'spreadsheet' => ['required', 'file', 'extensions:xlsx', 'max:10240'],
        ]);
        $summary = app(StudentImportService::class)->import($validated['spreadsheet']);

        return redirect()->route('students.index')->with('import_summary', $summary);
    }

    private function syncLabels(Student $student): void
    {
        $student->class_name = SchoolClass::query()->findOrFail($student->class_id)->name;
        $student->major = Major::query()->findOrFail($student->major_id)->name;
    }
}
