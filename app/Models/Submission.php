<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'site_id',
        'latitude',
        'longitude',
        'distance_from_site',
        'active_power',
        'voltage',
        'current',
        'load_percent',
        'energy_reading',
        'notes',
        'photo_path',
        'equipment_photo_path',
        'suspicious_flag',
        'risk_level',
        'gps_recorded_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'distance_from_site' => 'integer',
            'active_power' => 'decimal:2',
            'voltage' => 'decimal:2',
            'current' => 'decimal:2',
            'load_percent' => 'decimal:2',
            'energy_reading' => 'decimal:2',
            'suspicious_flag' => 'boolean',
            'gps_recorded_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function suspiciousLogs(): HasMany
    {
        return $this->hasMany(SuspiciousLog::class);
    }
}
