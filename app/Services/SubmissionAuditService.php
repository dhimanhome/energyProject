<?php

namespace App\Services;

use App\Jobs\SendSuspiciousSubmissionAlert;
use App\Models\Submission;
use App\Models\SuspiciousLog;

class SubmissionAuditService
{
    public function __construct(private readonly DistanceService $distanceService)
    {
    }

    /**
     * @return array<int, SuspiciousLog>
     */
    public function inspect(Submission $submission): array
    {
        $logs = [];

        if ($submission->distance_from_site > 500) {
            $logs[] = $this->log($submission, 'distance_exceeded', 'critical', sprintf(
                'Submission is %s meters away from assigned site.',
                number_format($submission->distance_from_site)
            ));
        } elseif ($submission->distance_from_site > 100) {
            $logs[] = $this->log($submission, 'distance_warning', 'warning', sprintf(
                'Submission is %s meters away from assigned site.',
                number_format($submission->distance_from_site)
            ));
        }

        if (! $submission->photo_path || ! $submission->equipment_photo_path) {
            $logs[] = $this->log($submission, 'missing_photo', 'warning', 'Required photo proof is missing.');
        }

        if ($this->hasRepeatedGps($submission)) {
            $logs[] = $this->log($submission, 'repeated_gps', 'warning', 'Employee reused the same GPS coordinates recently.');
        }

        if ($this->hasDuplicateEntry($submission)) {
            $logs[] = $this->log($submission, 'duplicate_entry', 'warning', 'Similar reading was submitted for this site recently.');
        }

        if ($submission->created_at->hour < 6 || $submission->created_at->hour > 21) {
            $logs[] = $this->log($submission, 'late_submission', 'warning', 'Submission was created outside normal field hours.');
        }

        if ($logs !== []) {
            $submission->forceFill([
                'suspicious_flag' => true,
                'risk_level' => collect($logs)->contains(fn ($log) => $log->severity === 'critical') ? 'suspicious' : $submission->risk_level,
            ])->save();

            SendSuspiciousSubmissionAlert::dispatch($submission->id);
        }

        return $logs;
    }

    private function hasRepeatedGps(Submission $submission): bool
    {
        return Submission::query()
            ->whereKeyNot($submission->id)
            ->where('employee_id', $submission->employee_id)
            ->where('latitude', $submission->latitude)
            ->where('longitude', $submission->longitude)
            ->where('created_at', '>=', now()->subDays(7))
            ->exists();
    }

    private function hasDuplicateEntry(Submission $submission): bool
    {
        return Submission::query()
            ->whereKeyNot($submission->id)
            ->where('employee_id', $submission->employee_id)
            ->where('site_id', $submission->site_id)
            ->where('energy_reading', $submission->energy_reading)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->exists();
    }

    private function log(Submission $submission, string $type, string $severity, string $message): SuspiciousLog
    {
        return SuspiciousLog::create([
            'submission_id' => $submission->id,
            'employee_id' => $submission->employee_id,
            'site_id' => $submission->site_id,
            'type' => $type,
            'severity' => $severity,
            'message' => $message,
            'context' => [
                'distance_from_site' => $submission->distance_from_site,
                'risk_level' => $this->distanceService->riskLevel($submission->distance_from_site),
            ],
        ]);
    }
}
