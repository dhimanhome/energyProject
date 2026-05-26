<?php

namespace App\Livewire;

use App\Models\Site;
use App\Models\Submission;
use Livewire\Component;

class LiveMap extends Component
{
    public function render()
    {
        return view('livewire.live-map', [
            'sites' => Site::query()->where('status', 'active')->get(),
            'submissions' => Submission::with(['employee', 'site'])->latest()->limit(200)->get(),
        ]);
    }
}
