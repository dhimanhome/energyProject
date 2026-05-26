<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionAudited implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Submission $submission)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('operations-dashboard');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->submission->id,
            'employee' => $this->submission->employee?->name,
            'site' => $this->submission->site?->site_name,
            'distance_from_site' => $this->submission->distance_from_site,
            'risk_level' => $this->submission->risk_level,
        ];
    }
}
