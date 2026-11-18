<?php

namespace App\Livewire\Settings;

use App\Models\SchoolSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public string $schoolName = '';

    public string $npsn = '';

    public string $address = '';

    public string $phone = '';

    public string $email = '';

    public string $principalName = '';

    public string $principalNip = '';

    public $logo = null;

    public function mount(): void
    {
        $school = SchoolSetting::query()->firstOrFail();
        $this->schoolName = $school->school_name;
        $this->npsn = $school->npsn ?? '';
        $this->address = $school->address ?? '';
        $this->phone = $school->phone ?? '';
        $this->email = $school->email ?? '';
        $this->principalName = $school->principal_name;
        $this->principalNip = $school->principal_nip ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'schoolName' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'principalName' => ['required', 'string', 'max:120'],
            'principalNip' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $school = SchoolSetting::query()->firstOrFail();
        $previousLogo = $school->logo_path;
        $school->update([
            'school_name' => $validated['schoolName'],
            'npsn' => $validated['npsn'] ?: null,
            'address' => $validated['address'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'email' => $validated['email'] ?: null,
            'principal_name' => $validated['principalName'],
            'principal_nip' => $validated['principalNip'] ?: null,
            'logo_path' => $this->logo ? $this->logo->storePublicly('school-logos', 'public') : $previousLogo,
        ]);
        if ($this->logo && $previousLogo) {
            Storage::disk('public')->delete($previousLogo);
        }
        $this->reset('logo');
        session()->flash('success', 'Identitas sekolah berhasil diperbarui.');
    }

    public function render(): View
    {
        return view('livewire.settings.edit', ['school' => SchoolSetting::query()->firstOrFail()])
            ->layout('layouts.app', ['title' => 'Pengaturan sekolah', 'breadcrumb' => 'Pengaturan sekolah']);
    }
}
