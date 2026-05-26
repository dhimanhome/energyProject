<?php

namespace App\Actions;

use App\Models\Employee;
use App\Events\SubmissionAudited;
use App\Models\Site;
use App\Models\Submission;
use App\Services\DistanceService;
use App\Services\PhotoStorageService;
use App\Services\SubmissionAuditService;
use Illuminate\Http\UploadedFile;

class StoreReadingSubmission
{
    public function __construct(
        private readonly DistanceService $distanceService,
        private readonly PhotoStorageService $photoStorage,
        private readonly SubmissionAuditService $auditService,
    ) {
    }

    public function handle(array $data, ?UploadedFile $meterPhoto = null, ?UploadedFile $equipmentPhoto = null): Submission
    {
        $site = Site::findOrFail($data['site_id']);
        $employee = Employee::findOrFail($data['employee_id']);
        $distance = $this->distanceService->meters(
            (float) $site->latitude,
            (float) $site->longitude,
            (float) $data['latitude'],
            (float) $data['longitude'],
        );
        $risk = $this->distanceService->riskLevel($distance);
        $metadata = [
            'allowed_radius' => $site->allowed_radius,
            'distance_rule' => '0-100m normal, 100-500m warning, >500m suspicious',
            'assigned_to_site' => $employee->sites()->whereKey($site->id)->exists(),
        ];

        if ($meterPhoto) {
            $metadata['meter_photo_exif'] = $this->photoStorage->exif($meterPhoto);
        }

        if ($equipmentPhoto) {
            $metadata['equipment_photo_exif'] = $this->photoStorage->exif($equipmentPhoto);
        }

        $submission = Submission::create([
            'employee_id' => $employee->id,
            'site_id' => $site->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'distance_from_site' => $distance,
            'active_power' => $data['active_power'],
            'voltage' => $data['voltage'] ?? 0,
            'current' => $data['current'] ?? 0,
            'load_percent' => $data['load_percent'] ?? 0,
            'energy_reading' => $data['energy_reading'],
            'notes' => $data['notes'] ?? null,
            'photo_path' => $meterPhoto ? $this->photoStorage->storeSubmissionPhoto($meterPhoto, 'meter') : null,
            'equipment_photo_path' => $equipmentPhoto ? $this->photoStorage->storeSubmissionPhoto($equipmentPhoto, 'equipment') : null,
            'suspicious_flag' => $risk === 'suspicious',
            'risk_level' => $risk,
            'gps_recorded_at' => $data['timestamp'] ?? now(),
            'metadata' => $metadata,
        ]);

        $employee->forceFill(['last_seen' => now()])->save();
        $employee->user?->forceFill(['last_seen_at' => now()])->save();
        $this->auditService->inspect($submission);
        SubmissionAudited::dispatch($submission->refresh()->load(['employee', 'site']));

        return $submission->refresh()->load(['employee', 'site', 'suspiciousLogs']);
    }
}
