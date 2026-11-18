<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CompanySpreadsheetService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyController extends Controller
{
    public function template(CompanySpreadsheetService $spreadsheets): StreamedResponse
    {
        return $spreadsheets->template();
    }

    public function export(CompanySpreadsheetService $spreadsheets): StreamedResponse
    {
        return $spreadsheets->export();
    }

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $companies = Company::query()
            ->withCount('internships')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($companyQuery) use ($search): void {
                    $companyQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('industry', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('companies.index', compact('companies', 'search'));
    }

    public function create(): View
    {
        return view('companies.form', ['company' => new Company]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'industry' => ['nullable', 'string', 'max:120'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);

        Company::create($validated);

        return redirect()->route('companies.index')->with('success', 'Data mitra berhasil ditambahkan.');
    }

    public function edit(Company $company): View
    {
        return view('companies.form', compact('company'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($company)],
            'industry' => ['nullable', 'string', 'max:120'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')->with('success', 'Data mitra berhasil diperbarui.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        if ($company->internships()->exists()) {
            return back()->with('error', 'Mitra memiliki riwayat Prakerin dan tidak dapat dihapus.');
        }

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Data mitra berhasil dihapus.');
    }
}
