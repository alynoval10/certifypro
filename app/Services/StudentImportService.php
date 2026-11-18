<?php

namespace App\Services;

use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentImportService
{
    /**
     * @return array{imported: int, skipped: int, errors: list<string>}
     */
    public function import(UploadedFile $file): array
    {
        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
        if ($sheet->getHighestRow() > 2001) {
            throw ValidationException::withMessages(['spreadsheet' => 'File dibatasi maksimal 2.000 baris data.']);
        }

        $rows = $sheet->toArray(null, true, true, true);
        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), array_shift($rows) ?? []);
        $aliases = [
            'student_number' => ['nisnisn', 'nis', 'nisn'],
            'name' => ['nama', 'namalengkap'],
            'status' => ['status', 'statussiswa'],
            'class_name' => ['kelas'],
            'major' => ['jurusan', 'programkeahlian'],
            'gender' => ['jeniskelaminlp', 'jeniskelamin', 'gender'],
            'email' => ['email'],
            'phone' => ['telepon', 'nomortelepon', 'nohp', 'phone'],
            'address' => ['alamat', 'address'],
        ];
        $columns = [];
        foreach ($aliases as $field => $options) {
            foreach ($headers as $column => $header) {
                if (in_array($header, $options, true)) {
                    $columns[$field] = $column;
                    break;
                }
            }
        }
        foreach (['student_number', 'name', 'class_name', 'major'] as $requiredColumn) {
            if (! isset($columns[$requiredColumn])) {
                throw ValidationException::withMessages(['spreadsheet' => 'Kolom wajib harus ada: NIS/NISN, Nama, Kelas, dan Jurusan. Unduh template agar formatnya sesuai.']);
            }
        }

        $seenNumbers = [];
        $seenEmails = [];
        $imported = 0;
        $skipped = 0;
        $errors = [];
        foreach ($rows as $rowNumber => $row) {
            $data = [];
            foreach ($columns as $field => $column) {
                $data[$field] = trim((string) ($row[$column] ?? ''));
            }
            foreach (['gender', 'email', 'phone', 'address'] as $optionalField) {
                $data[$optionalField] ??= '';
            }
            if (collect($data)->every(fn ($value) => $value === '')) {
                continue;
            }

            $data['status'] ??= 'aktif';
            $data['gender'] = match (mb_strtoupper($data['gender'] ?? '')) {
                'L', 'LAKI-LAKI', 'LAKI LAKI', 'M' => 'L',
                'P', 'PEREMPUAN', 'F' => 'P',
                '' => null,
                default => $data['gender'],
            };
            $data['email'] = $data['email'] !== '' ? $data['email'] : null;
            $data['status'] = match (mb_strtolower(trim($data['status']))) {
                '', 'aktif', 'active' => 'active',
                'lulus', 'lulus sekolah', 'graduated', 'alumni' => 'graduated',
                default => $data['status'],
            };
            $validator = Validator::make($data, [
                'student_number' => ['required', 'string', 'max:30', 'unique:students,student_number'],
                'name' => ['required', 'string', 'max:255'],
                'status' => ['required', Rule::in(['active', 'graduated'])],
                'class_name' => ['required', 'string', 'max:50'],
                'major' => ['required', 'string', 'max:120'],
                'gender' => ['nullable', Rule::in(['L', 'P'])],
                'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
                'phone' => ['nullable', 'string', 'max:30'],
                'address' => ['nullable', 'string', 'max:2000'],
            ]);
            if (isset($seenNumbers[$data['student_number'] ?? ''])) {
                $validator->errors()->add('student_number', 'NIS/NISN duplikat di file.');
            }
            if ($data['email'] !== null && isset($seenEmails[mb_strtolower($data['email'])])) {
                $validator->errors()->add('email', 'Email duplikat di file.');
            }
            if ($validator->fails()) {
                $skipped++;
                if (count($errors) < 20) {
                    $errors[] = 'Baris '.($rowNumber + 2).': '.$validator->errors()->first();
                }

                continue;
            }

            $seenNumbers[$data['student_number']] = true;
            if ($data['email'] !== null) {
                $seenEmails[mb_strtolower($data['email'])] = true;
            }
            $class = SchoolClass::query()->firstOrCreate(['name' => $data['class_name']]);
            $major = Major::query()->firstOrCreate(['name' => $data['major']]);
            Student::query()->create([
                'student_number' => $data['student_number'],
                'name' => $data['name'],
                'status' => $data['status'],
                'class_id' => $class->id,
                'major_id' => $major->id,
                'class_name' => $class->name,
                'major' => $major->name,
                'gender' => $data['gender'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?: null,
                'address' => $data['address'] ?: null,
            ]);
            $imported++;
        }

        return compact('imported', 'skipped', 'errors');
    }

    private function normalizeHeader(string $header): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower($header)) ?? '';
    }
}
