<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanySpreadsheetService
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
            'name' => ['namaperusahaan', 'namausaha', 'dudi'],
            'industry' => ['bidangindustri', 'bidangusaha', 'industri'],
            'leader_name' => ['namapimpinan', 'pimpinan', 'namadirektur'],
            'mentor_name' => ['namapembimbingdudi', 'pembimbingdudi', 'pembimbingindustri'],
            'contact_name' => ['namakontak', 'kontak'],
            'email' => ['email'],
            'phone' => ['nomortelepon', 'telepon', 'nohp', 'phone'],
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
        if (! isset($columns['name'])) {
            throw ValidationException::withMessages(['spreadsheet' => 'Kolom wajib Nama Perusahaan tidak ditemukan. Unduh template agar formatnya sesuai.']);
        }

        $seenNames = [];
        $imported = 0;
        $skipped = 0;
        $errors = [];
        foreach ($rows as $rowNumber => $row) {
            $data = [];
            foreach ($columns as $field => $column) {
                $data[$field] = trim((string) ($row[$column] ?? ''));
            }
            if (collect($data)->every(fn (string $value): bool => $value === '')) {
                continue;
            }
            foreach (['industry', 'leader_name', 'mentor_name', 'contact_name', 'email', 'phone', 'address'] as $field) {
                $data[$field] = ($data[$field] ?? '') !== '' ? $data[$field] : null;
            }
            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
                'industry' => ['nullable', 'string', 'max:120'],
                'leader_name' => ['nullable', 'string', 'max:255'],
                'mentor_name' => ['nullable', 'string', 'max:255'],
                'contact_name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'address' => ['nullable', 'string', 'max:2000'],
            ]);
            $normalizedName = mb_strtolower($data['name'] ?? '');
            if (isset($seenNames[$normalizedName])) {
                $validator->errors()->add('name', 'Nama perusahaan duplikat di file.');
            }
            if ($validator->fails()) {
                $skipped++;
                if (count($errors) < 20) {
                    $errors[] = 'Baris '.($rowNumber + 2).': '.$validator->errors()->first();
                }

                continue;
            }

            $seenNames[$normalizedName] = true;
            Company::query()->create($data);
            $imported++;
        }

        return compact('imported', 'skipped', 'errors');
    }

    public function template(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[
            'Nama Perusahaan', 'Bidang Industri', 'Nama Pimpinan', 'Nama Pembimbing DUDI',
            'Nama Kontak', 'Email', 'Nomor Telepon', 'Alamat',
        ]]);
        $sheet->fromArray([['PT Contoh Industri', 'Teknologi Informasi', 'Budi Santoso', 'Siti Aminah', 'Admin', 'contoh@email.com', '08123456789', 'Alamat perusahaan']], null, 'A2');

        return $this->download($spreadsheet, 'template-mitra-dudi.xlsx');
    }

    public function export(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[
            'Nama Perusahaan', 'Bidang Industri', 'Nama Pimpinan', 'Nama Pembimbing DUDI',
            'Nama Kontak', 'Email', 'Nomor Telepon', 'Alamat',
        ]]);
        foreach (Company::query()->orderBy('name')->cursor() as $company) {
            $sheet->fromArray([[
                $company->name, $company->industry, $company->leader_name, $company->mentor_name,
                $company->contact_name, $company->email, $company->phone, $company->address,
            ]], null, 'A'.($sheet->getHighestRow() + 1));
        }

        return $this->download($spreadsheet, 'data-mitra-dudi.xlsx');
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
