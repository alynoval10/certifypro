<?php

namespace App\Livewire\CertificateTemplates;

use App\Models\CertificateTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Index extends Component
{
    #[Locked]
    public ?int $editingId = null;

    public bool $showForm = false;

    public array $form = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $templateId): void
    {
        $template = CertificateTemplate::query()->findOrFail($templateId);
        if ($template->certificates()->exists()) {
            session()->flash('error', 'Template yang sudah digunakan tidak dapat diubah agar sertifikat lama tetap konsisten. Buat template baru untuk desain lain.');

            return;
        }
        $this->editingId = $template->id;
        $this->form = $template->only([
            'name', 'front_heading', 'front_subheading', 'back_heading', 'front_company_sign_label',
            'front_school_sign_label', 'back_mentor_sign_label', 'primary_color', 'accent_color',
        ]);
        $this->showForm = true;
    }

    public function save(): void
    {
        $template = $this->editingId ? CertificateTemplate::query()->findOrFail($this->editingId) : new CertificateTemplate;
        $validated = $this->validate([
            'form.name' => ['required', 'string', 'max:120', Rule::unique('certificate_templates', 'name')->ignore($template)],
            'form.front_heading' => ['required', 'string', 'max:160'],
            'form.front_subheading' => ['required', 'string', 'max:255'],
            'form.back_heading' => ['required', 'string', 'max:160'],
            'form.front_company_sign_label' => ['required', 'string', 'max:120'],
            'form.front_school_sign_label' => ['required', 'string', 'max:120'],
            'form.back_mentor_sign_label' => ['required', 'string', 'max:120'],
            'form.primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'form.accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ])['form'];
        $template->fill($validated)->save();
        $this->showForm = false;
        $this->editingId = null;
        session()->flash('success', 'Desain template berhasil disimpan. Aktifkan untuk menggunakannya pada sertifikat baru.');
    }

    public function activate(int $templateId): void
    {
        $template = CertificateTemplate::query()->findOrFail($templateId);
        CertificateTemplate::query()->update(['is_active' => false]);
        $template->update(['is_active' => true]);
        session()->flash('success', 'Template '.$template->name.' aktif untuk sertifikat baru.');
    }

    public function delete(int $templateId): void
    {
        $template = CertificateTemplate::query()->findOrFail($templateId);
        if ($template->is_active || $template->certificates()->exists()) {
            session()->flash('error', 'Template aktif atau template yang sudah digunakan tidak dapat dihapus.');

            return;
        }
        $template->delete();
        session()->flash('success', 'Template berhasil dihapus.');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.certificate-templates.index', [
            'templates' => CertificateTemplate::query()->withCount('certificates')->orderByDesc('is_active')->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Template sertifikat', 'breadcrumb' => 'Template sertifikat']);
    }

    private function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'front_heading' => 'Sertifikat Praktik Kerja Industri',
            'front_subheading' => 'Dengan ini menerangkan bahwa',
            'back_heading' => 'Rincian Penilaian Prakerin',
            'front_company_sign_label' => 'Pimpinan DUDI',
            'front_school_sign_label' => 'Kepala Sekolah',
            'back_mentor_sign_label' => 'Pembimbing DUDI',
            'primary_color' => '#1e1b4b',
            'accent_color' => '#b58b45',
        ];
    }
}
