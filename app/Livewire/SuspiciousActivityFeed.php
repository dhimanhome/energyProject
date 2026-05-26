<?php

namespace App\Livewire;

use App\Models\SuspiciousLog;
use Livewire\Component;

class SuspiciousActivityFeed extends Component
{
    public function render()
    {
        return view('livewire.suspicious-activity-feed', [
            'logs' => SuspiciousLog::with(['employee', 'site', 'submission'])->latest()->limit(12)->get(),
        ]);
    }
}
