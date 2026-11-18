<?php

namespace App\Services;

use App\Models\Teacher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherSpreadsheetService
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
        $headers = array_map(fn ($value): string => $this->normalizeHeader((string) $value), array_shift($rows) ?? []);
        $aliases = [
            'name' => ['namaguru', 'namalengkap', 'nama'],
            'nip' => ['nip', 'nomorindukpegawai'],
            'email' => ['email'],
            'phone' => ['nomortelepon', 'telepon', 'nohp', 'phone'],
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
        if (! isset($columns['name'])) {
            throw ValidationException::withMessages(['spreadsheet' => 'Kolom Nama Guru wajib ada. Unduh template Excel agar formatnya sesuai.']);
        }

        $seenNips = [];
        $imported = 0;
        $skipped = 0;
        $errors = [];
        foreach ($rows as $rowNumber => $row) {
            $data = [];
            foreach ($columns as $field => $column) {
                $value = trim((string) ($row[$column] ?? ''));
                $data[$field] = $value === '' ? null : $value;
            }
            if (collect($data)->every(fn (?string $value): bool => $value === null)) {
                continue;
            }

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'nip' => ['nullable', 'string', 'max:30', 'unique:teachers,nip'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
            ]);
            $nip = $data['nip'] ?? null;
            if ($nip !== null && isset($seenNips[$nip])) {
                $validator->errors()->add('nip', 'NIP duplikat di file.');
            }
            if ($validator->fails()) {
                $skipped++;
                if (count($errors) < 20) {
                    $errors[] = 'Baris '.($rowNumber + 2).': '.$validator->errors()->first();
                }

                continue;
            }

            if ($nip !== null) {
                $seenNips[$nip] = true;
            }
            Teacher::query()->create($data);
            $imported++;
        }

        return compact('imported', 'skipped', 'errors');
    }

    public function template(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([['Nama Guru', 'NIP', 'Email', 'Nomor Telepon']]);

        return $this->download($spreadsheet, 'template-guru-pembimbing.xlsx');
    }

    public function export(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['Nama Guru', 'NIP', 'Email', 'Nomor Telepon']]);
        foreach (Teacher::query()->orderBy('name')->cursor() as $teacher) {
            $sheet->fromArray([[$teacher->name, $teacher->nip, $teacher->email, $teacher->phone]], null, 'A'.($sheet->getHighestRow() + 1));
        }

        return $this->download($spreadsheet, 'data-guru-pembimbing.xlsx');
    }

    private function download(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function normalizeHeader(string $header): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower($header)) ?? '';
    }
}
