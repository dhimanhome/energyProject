<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_code',
        'site_name',
        'latitude',
        'longitude',
        'allowed_radius',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'allowed_radius' => 'integer',
        ];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot(['assigned_at', 'unassigned_at']);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function suspiciousLogs(): HasMany
    {
        return $this->hasMany(SuspiciousLog::class);
    }
}
