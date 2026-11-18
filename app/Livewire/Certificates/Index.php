<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    /** @var list<int|string> */
    public array $selectedIds = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $search = trim($this->search);

        return view('livewire.certificates.index', [
            'certificates' => Certificate::query()->with(['internship.student', 'internship.company'])
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $scope) use ($search): void {
                        $scope->where('certificate_number', 'like', "%{$search}%")
                            ->orWhereHas('internship.student', fn (Builder $students) => $students->where('name', 'like', "%{$search}%")->orWhere('student_number', 'like', "%{$search}%"));
                    });
                })
                ->when(in_array($this->statusFilter, ['issued', 'revoked'], true), fn (Builder $query) => $query->where('status', $this->statusFilter))
                ->latest('issued_at')->latest('id')->paginate(12),
        ])->layout('layouts.app', ['title' => 'Sertifikat', 'breadcrumb' => 'Sertifikat']);
    }
}
