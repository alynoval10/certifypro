<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $presets = [
            [
                'name' => 'Navy Modern',
                'front_heading' => 'Sertifikat Praktik Kerja Industri',
                'front_subheading' => 'Diberikan sebagai penghargaan atas penyelesaian',
                'back_heading' => 'Rekap Kompetensi dan Penilaian',
                'front_company_sign_label' => 'Pimpinan Mitra Industri',
                'front_school_sign_label' => 'Kepala Sekolah',
                'back_mentor_sign_label' => 'Pembimbing Industri',
                'primary_color' => '#12304A',
                'accent_color' => '#38A6B5',
            ],
            [
                'name' => 'Hutan Prestasi',
                'front_heading' => 'Sertifikat Kompetensi Prakerin',
                'front_subheading' => 'Dianugerahkan kepada peserta didik yang telah menuntaskan',
                'back_heading' => 'Capaian Kompetensi Praktik',
                'front_company_sign_label' => 'Pimpinan DUDI',
                'front_school_sign_label' => 'Kepala Sekolah',
                'back_mentor_sign_label' => 'Mentor Industri',
                'primary_color' => '#174C3C',
                'accent_color' => '#91A85A',
            ],
            [
                'name' => 'Marun Akademik',
                'front_heading' => 'Sertifikat Praktik Kerja Industri',
                'front_subheading' => 'Sebagai pengakuan atas dedikasi dan pencapaian selama',
                'back_heading' => 'Transkrip Hasil Praktik Industri',
                'front_company_sign_label' => 'Pimpinan Perusahaan',
                'front_school_sign_label' => 'Kepala Sekolah',
                'back_mentor_sign_label' => 'Pembimbing DUDI',
                'primary_color' => '#512B3A',
                'accent_color' => '#C18476',
            ],
            [
                'name' => 'Teal Minimalis',
                'front_heading' => 'Sertifikat Penyelesaian Prakerin',
                'front_subheading' => 'Menerangkan bahwa peserta didik telah menyelesaikan',
                'back_heading' => 'Ringkasan Evaluasi Prakerin',
                'front_company_sign_label' => 'Pimpinan DUDI',
                'front_school_sign_label' => 'Kepala Sekolah',
                'back_mentor_sign_label' => 'Pembimbing Lapangan',
                'primary_color' => '#123F46',
                'accent_color' => '#35A69A',
            ],
            [
                'name' => 'Ungu Inovasi',
                'front_heading' => 'Sertifikat Pengalaman Industri',
                'front_subheading' => 'Diberikan atas partisipasi dan capaian dalam kegiatan',
                'back_heading' => 'Portofolio Penilaian Prakerin',
                'front_company_sign_label' => 'Pimpinan Mitra',
                'front_school_sign_label' => 'Kepala Sekolah',
                'back_mentor_sign_label' => 'Pembimbing Industri',
                'primary_color' => '#392C67',
                'accent_color' => '#A58AE0',
            ],
        ];

        DB::table('certificate_templates')->insertOrIgnore(array_map(fn (array $preset): array => [
            ...$preset,
            'is_active' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ], $presets));
    }

    public function down(): void
    {
        $presetIds = DB::table('certificate_templates')
            ->whereIn('name', ['Navy Modern', 'Hutan Prestasi', 'Marun Akademik', 'Teal Minimalis', 'Ungu Inovasi'])
            ->pluck('id');

        DB::table('certificate_templates')
            ->whereIn('id', $presetIds)
            ->whereNotIn('id', DB::table('certificates')->whereNotNull('certificate_template_id')->select('certificate_template_id'))
            ->delete();
    }
};
