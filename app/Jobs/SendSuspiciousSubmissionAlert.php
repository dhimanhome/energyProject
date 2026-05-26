<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\SuspiciousSubmissionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Permission\Models\Role;

class SendSuspiciousSubmissionAlert implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $submissionId)
    {
    }

    public function handle(): void
    {
        $submission = Submission::with(['employee', 'site'])->find($this->submissionId);

        if (! $submission) {
            return;
        }

        $notification = new SuspiciousSubmissionNotification($submission);

        $roles = Role::query()
            ->whereIn('name', ['Admin', 'Supervisor'])
            ->pluck('name')
            ->all();

        if ($roles !== []) {
            User::role($roles)
                ->where('status', 'active')
                ->get()
                ->each(fn (User $user) => $user->notify($notification));
        }

        $notification->sendTelegram();
    }
}
