<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Internship;
use App\Models\SchoolSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();

        $certificates = Certificate::query()
            ->with(['internship.student', 'internship.company'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('internship.student', function ($studentQuery) use ($search): void {
                        $studentQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('student_number', 'like', "%{$search}%");
                    });
            })
            ->when(in_array($status, ['issued', 'revoked'], true), fn ($query) => $query->where('status', $status))
            ->latest('issued_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('certificates.index', compact('certificates', 'search', 'status'));
    }

    public function create(): View|RedirectResponse
    {
        $internships = Internship::query()
            ->where('status', 'completed')
            ->whereDoesntHave('certificate')
            ->with(['student', 'company'])
            ->orderByDesc('end_date')
            ->get();

        if ($internships->isEmpty()) {
            return redirect()->route('internships.index')->with('error', 'Selesaikan data Prakerin terlebih dahulu sebelum menerbitkan sertifikat.');
        }

        return view('certificates.create', compact('internships'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'internship_id' => ['required', 'integer', 'exists:internships,id'],
            'predicate' => ['required', 'string', 'in:Sangat Baik,Baik,Cukup'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $certificate = DB::transaction(function () use ($validated): Certificate {
            $internship = Internship::query()->lockForUpdate()->findOrFail($validated['internship_id']);

            abort_unless($internship->status === 'completed', 422, 'Prakerin harus berstatus selesai sebelum sertifikat diterbitkan.');
            abort_if($internship->certificate()->exists(), 422, 'Sertifikat untuk data Prakerin ini sudah diterbitkan.');

            $certificate = $internship->certificate()->create([
                'verification_code' => (string) Str::uuid(),
                'issued_at' => today(),
                'predicate' => $validated['predicate'],
                'description' => $validated['description'] ?? null,
                'status' => 'issued',
            ]);

            $certificate->update([
                'certificate_number' => sprintf('PRK-%s-%05d', now()->format('Y'), $certificate->id),
            ]);

            return $certificate;
        });

        return redirect()->route('certificates.show', $certificate)->with('success', 'Sertifikat berhasil diterbitkan.');
    }

    public function show(Certificate $certificate): View
    {
        $certificate->load(['internship.student.schoolClass', 'internship.student.majorRelation', 'internship.company', 'internship.academicYear', 'internship.scores.component']);

        return view('certificates.show', [
            'certificate' => $certificate,
            'school' => SchoolSetting::query()->first(),
        ]);
    }

    public function verify(string $verificationCode): View
    {
        $certificate = Certificate::query()
            ->with(['internship.student', 'internship.company', 'internship.academicYear'])
            ->where('verification_code', $verificationCode)
            ->firstOrFail();

        return view('certificates.verify', [
            'certificate' => $certificate,
            'school' => SchoolSetting::query()->first(),
        ]);
    }

    public function revoke(Request $request, Certificate $certificate): RedirectResponse
    {
        if ($certificate->status === 'revoked') {
            return back()->with('error', 'Sertifikat ini sudah dicabut.');
        }

        $validated = $request->validate([
            'revoke_reason' => ['required', 'string', 'max:1000'],
        ]);

        $certificate->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoke_reason' => $validated['revoke_reason'],
        ]);

        return redirect()->route('certificates.show', $certificate)->with('success', 'Sertifikat berhasil dicabut.');
    }
}
