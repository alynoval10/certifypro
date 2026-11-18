<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', ['school' => SchoolSetting::query()->firstOrFail()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $school = SchoolSetting::query()->firstOrFail();

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'principal_name' => ['required', 'string', 'max:120'],
            'principal_nip' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $previousLogo = $school->logo_path;

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->storePublicly('school-logos', 'public');
        }

        unset($validated['logo']);
        $school->update($validated);

        if ($request->hasFile('logo') && $previousLogo) {
            Storage::disk('public')->delete($previousLogo);
        }

        return redirect()->route('settings.edit')->with('success', 'Identitas sekolah berhasil diperbarui.');
    }
}
