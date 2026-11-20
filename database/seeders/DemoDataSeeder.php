<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Company;
use App\Models\Internship;
use App\Models\InternshipScore;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\ScoreComponent;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! SchoolSetting::query()->exists()) {
            SchoolSetting::query()->create([
                'school_name' => 'SMK Nusantara Demo',
                'npsn' => '99990001',
                'address' => 'Jl. Pendidikan No. 1, Kabupaten Contoh',
                'phone' => '(021) 555-0101',
                'email' => 'info@smknusantara-demo.sch.id',
                'principal_name' => 'Drs. Budi Santoso',
            ]);
        }

        $class = SchoolClass::query()->firstOrCreate(['name' => 'XII TKJ 1']);
        $major = Major::query()->firstOrCreate(['name' => 'Teknik Komputer dan Jaringan']);

        $startYear = now()->month >= 7 ? now()->year : now()->year - 1;
        $academicYearName = $startYear.'/'.($startYear + 1);
        $academicYear = AcademicYear::query()->firstOrCreate(
            ['name' => $academicYearName],
            [
                'starts_on' => $startYear.'-07-01',
                'ends_on' => ($startYear + 1).'-06-30',
                'is_active' => false,
            ],
        );
        if (! AcademicYear::query()->where('is_active', true)->exists()) {
            $academicYear->update(['is_active' => true]);
        }

        $teacher = Teacher::query()->firstOrCreate(
            ['nip' => 'DEMO-GURU-001'],
            ['name' => 'Rina Puspita, S.Kom.', 'email' => 'rina.demo@example.test', 'phone' => '0812-0000-0001'],
        );

        $company = Company::query()->firstOrCreate(
            ['name' => 'PT Teknologi Nusantara Demo'],
            [
                'industry' => 'Teknologi Informasi',
                'leader_name' => 'Andi Pratama',
                'mentor_name' => 'Dewi Lestari',
                'contact_name' => 'Dewi Lestari',
                'email' => 'kontak@teknusantara.example.test',
                'phone' => '021-555-0202',
                'address' => 'Jl. Industri Digital No. 10, Kabupaten Contoh',
            ],
        );

        $secondCompany = Company::query()->firstOrCreate(
            ['name' => 'CV Kreatif Digital Demo'],
            [
                'industry' => 'Desain dan Multimedia',
                'leader_name' => 'Sari Wulandari',
                'mentor_name' => 'Fajar Nugraha',
                'contact_name' => 'Fajar Nugraha',
                'email' => 'halo@kreatifdigital.example.test',
                'phone' => '021-555-0303',
                'address' => 'Jl. Kreatif No. 5, Kabupaten Contoh',
            ],
        );

        $completedStudent = $this->makeStudent('DEMO-2026-001', 'Alya Putri Ramadhan', 'P', $class, $major);
        $ongoingStudent = $this->makeStudent('DEMO-2026-002', 'Rizky Maulana', 'L', $class, $major);
        $this->makeStudent('DEMO-2026-003', 'Nadia Safitri', 'P', $class, $major);

        $completedInternship = Internship::query()->firstOrCreate(
            ['student_id' => $completedStudent->id, 'academic_year_id' => $academicYear->id],
            [
                'company_id' => $company->id,
                'academic_year' => $academicYearName,
                'academic_year_id' => $academicYear->id,
                'department' => 'Pengembangan Perangkat Lunak',
                'mentor_name' => $company->mentor_name,
                'school_supervisor' => $teacher->name,
                'school_supervisor_id' => $teacher->id,
                'start_date' => now()->subMonths(4)->toDateString(),
                'end_date' => now()->subMonths(2)->toDateString(),
                'score' => 90,
                'final_note' => 'Menunjukkan disiplin dan kemampuan kerja yang sangat baik.',
                'status' => 'completed',
            ],
        );

        Internship::query()->firstOrCreate(
            ['student_id' => $ongoingStudent->id, 'academic_year_id' => $academicYear->id],
            [
                'company_id' => $secondCompany->id,
                'academic_year' => $academicYearName,
                'academic_year_id' => $academicYear->id,
                'department' => 'Desain Grafis',
                'mentor_name' => $secondCompany->mentor_name,
                'school_supervisor' => $teacher->name,
                'school_supervisor_id' => $teacher->id,
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonths(2)->toDateString(),
                'score' => null,
                'final_note' => null,
                'status' => 'ongoing',
            ],
        );

        $components = [
            ['name' => 'Kedisiplinan', 'weight' => 25, 'sort_order' => 1],
            ['name' => 'Ketepatan waktu', 'weight' => 20, 'sort_order' => 2],
            ['name' => 'Kerajinan', 'weight' => 20, 'sort_order' => 3],
            ['name' => 'Tanggung jawab', 'weight' => 20, 'sort_order' => 4],
            ['name' => 'Kerja sama', 'weight' => 15, 'sort_order' => 5],
        ];
        foreach ($components as $componentData) {
            $component = ScoreComponent::query()->firstOrCreate(
                ['name' => $componentData['name']],
                ['max_score' => 100, 'weight' => $componentData['weight'], 'sort_order' => $componentData['sort_order'], 'is_active' => true],
            );
            InternshipScore::query()->firstOrCreate(
                ['internship_id' => $completedInternship->id, 'score_component_id' => $component->id],
                ['score' => 90 + $componentData['sort_order'], 'note' => 'Nilai demo untuk simulasi penilaian.'],
            );
        }
        $completedInternship->recalculateFinalScore();

        $template = CertificateTemplate::query()->firstOrCreate(
            ['name' => 'Demo Sertifikat'],
            [
                'front_heading' => 'Sertifikat Praktik Kerja Industri',
                'front_subheading' => 'Dengan ini menerangkan bahwa',
                'back_heading' => 'Rincian Penilaian Prakerin',
                'front_company_sign_label' => 'Pimpinan Mitra Industri',
                'front_school_sign_label' => 'Kepala Sekolah',
                'back_mentor_sign_label' => 'Pembimbing Industri',
                'primary_color' => '#1e1b4b',
                'accent_color' => '#b58b45',
                'is_active' => false,
            ],
        );

        Certificate::query()->firstOrCreate(
            ['certificate_number' => 'DEMO/CP/'.$startYear.'/001'],
            [
                'internship_id' => $completedInternship->id,
                'certificate_template_id' => $template->id,
                'verification_code' => (string) Str::uuid(),
                'issued_at' => now()->subMonth()->toDateString(),
                'predicate' => 'Sangat Baik',
                'description' => 'Sertifikat demo untuk mencoba proses verifikasi.',
                'status' => 'issued',
            ],
        );

        $this->command?->info('Data demo CertifyPro berhasil disiapkan (3 siswa, 2 mitra, 1 guru, 2 penempatan, dan 1 sertifikat).');
    }

    private function makeStudent(string $studentNumber, string $name, string $gender, SchoolClass $class, Major $major): Student
    {
        return Student::query()->firstOrCreate(
            ['student_number' => $studentNumber],
            [
                'name' => $name,
                'status' => 'active',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'class_name' => $class->name,
                'major' => $major->name,
                'gender' => $gender,
                'email' => null,
                'phone' => null,
                'address' => 'Alamat demo, Kabupaten Contoh',
            ],
        );
    }
}
