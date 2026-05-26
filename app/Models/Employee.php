<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'name',
        'phone',
        'email',
        'status',
        'last_seen',
    ];

    protected function casts(): array
    {
        return [
            'last_seen' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class)
            ->withPivot(['assigned_at', 'unassigned_at']);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function locationUpdates(): HasMany
    {
        return $this->hasMany(LocationUpdate::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(LocationUpdate::class)->latestOfMany();
    }

    public function suspiciousLogs(): HasMany
    {
        return $this->hasMany(SuspiciousLog::class);
    }

    public function isOnline(): bool
    {
        return $this->last_seen?->gt(now()->subMinutes(10)) ?? false;
    }
}
