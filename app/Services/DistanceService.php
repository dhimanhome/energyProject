<?php

namespace App\Services;

class DistanceService
{
    private const EARTH_RADIUS_METERS = 6371000;

    public function meters(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * (sin($deltaLon / 2) ** 2);

        return (int) round(2 * self::EARTH_RADIUS_METERS * asin(min(1, sqrt($a))));
    }

    public function riskLevel(int $meters): string
    {
        return match (true) {
            $meters > 500 => 'suspicious',
            $meters > 100 => 'warning',
            default => 'normal',
        };
    }
}
