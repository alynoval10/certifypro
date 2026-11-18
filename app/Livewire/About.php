<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class About extends Component
{
    public function render(): View
    {
        return view('about')->layout('layouts.app', ['title' => 'Tentang aplikasi', 'breadcrumb' => 'Tentang aplikasi']);
    }
}
