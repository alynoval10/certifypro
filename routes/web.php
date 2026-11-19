<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\FirstAdminController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Livewire\About;
use App\Livewire\Assessments\Index as AssessmentsIndex;
use App\Livewire\Backups\Index as BackupsIndex;
use App\Livewire\Certificates\Create as CreateCertificate;
use App\Livewire\Certificates\Index as CertificatesIndex;
use App\Livewire\Certificates\PrintBatch as PrintBatchCertificates;
use App\Livewire\Certificates\Show as ShowCertificate;
use App\Livewire\CertificateTemplates\Index as CertificateTemplatesIndex;
use App\Livewire\Companies\Index as CompaniesIndex;
use App\Livewire\Dashboard;
use App\Livewire\Internships\Index as InternshipsIndex;
use App\Livewire\SchoolStructure\Index as SchoolStructureIndex;
use App\Livewire\Settings\Edit as SettingsEdit;
use App\Livewire\Students\Index as StudentsIndex;
use App\Livewire\Teachers\Index as TeachersIndex;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route(User::query()->exists() ? 'login' : 'setup');
})->name('home');

Route::get('/tentang-aplikasi', About::class)->name('about');
Route::get('/verifikasi/{verificationCode}', [CertificateController::class, 'verify'])
    ->whereUuid('verificationCode')
    ->name('certificates.verify');

Route::middleware('guest')->group(function (): void {
    Route::get('/setup', [FirstAdminController::class, 'create'])->middleware('throttle:3,1')->name('setup');
    Route::post('/setup', [FirstAdminController::class, 'store'])->middleware('throttle:3,1')->name('setup.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/siswa', StudentsIndex::class)->name('students.index');
    Route::get('/siswa/create', StudentsIndex::class)->name('students.create');
    Route::get('/siswa/{student}/edit', StudentsIndex::class)->name('students.edit');
    Route::get('/siswa-template.xlsx', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::get('/mitra-template.xlsx', [CompanyController::class, 'template'])->name('companies.template');
    Route::get('/mitra-export.xlsx', [CompanyController::class, 'export'])->name('companies.export');
    Route::get('/mitra', CompaniesIndex::class)->name('companies.index');
    Route::get('/mitra/create', CompaniesIndex::class)->name('companies.create');
    Route::get('/mitra/{company}/edit', CompaniesIndex::class)->name('companies.edit');
    Route::get('/prakerin', InternshipsIndex::class)->name('internships.index');
    Route::get('/prakerin/create', InternshipsIndex::class)->name('internships.create');
    Route::get('/prakerin/{internship}/edit', InternshipsIndex::class)->name('internships.edit');
    Route::get('/data-sekolah', SchoolStructureIndex::class)->name('school-structure.index');
    Route::get('/guru-pembimbing-template.xlsx', [TeacherController::class, 'template'])->name('teachers.template');
    Route::get('/guru-pembimbing-export.xlsx', [TeacherController::class, 'export'])->name('teachers.export');
    Route::get('/guru-pembimbing', TeachersIndex::class)->name('teachers.index');
    Route::get('/penilaian', AssessmentsIndex::class)->name('assessments.index');
    Route::get('/sertifikat', CertificatesIndex::class)->name('certificates.index');
    Route::get('/sertifikat/cetak-massal', PrintBatchCertificates::class)->name('certificates.print-batch');
    Route::get('/sertifikat/buat', CreateCertificate::class)->name('certificates.create');
    Route::get('/sertifikat/{certificate}', ShowCertificate::class)->name('certificates.show');
    Route::get('/template-sertifikat', CertificateTemplatesIndex::class)->name('certificate-templates.index');
    Route::get('/pengaturan', SettingsEdit::class)->name('settings.edit');
    Route::get('/backup-data', BackupsIndex::class)->name('backups.index');
});
