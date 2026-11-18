<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('front_heading', 160)->default('Sertifikat Praktik Kerja Industri');
            $table->string('front_subheading', 255)->default('Dengan ini menerangkan bahwa');
            $table->string('back_heading', 160)->default('Rincian Penilaian Prakerin');
            $table->string('front_company_sign_label', 120)->default('Pimpinan DUDI');
            $table->string('front_school_sign_label', 120)->default('Kepala Sekolah');
            $table->string('back_mentor_sign_label', 120)->default('Pembimbing DUDI');
            $table->string('primary_color', 7)->default('#1e1b4b');
            $table->string('accent_color', 7)->default('#b58b45');
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        DB::table('certificate_templates')->insert([
            'name' => 'Klasik Emas',
            'front_heading' => 'Sertifikat Praktik Kerja Industri',
            'front_subheading' => 'Dengan ini menerangkan bahwa',
            'back_heading' => 'Rincian Penilaian Prakerin',
            'front_company_sign_label' => 'Pimpinan DUDI',
            'front_school_sign_label' => 'Kepala Sekolah',
            'back_mentor_sign_label' => 'Pembimbing DUDI',
            'primary_color' => '#1e1b4b',
            'accent_color' => '#b58b45',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
