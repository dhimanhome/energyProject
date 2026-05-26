<?php

namespace Tests\Unit;

use App\Services\DistanceService;
use PHPUnit\Framework\TestCase;

class DistanceServiceTest extends TestCase
{
    public function test_it_calculates_haversine_distance_in_meters(): void
    {
        $service = new DistanceService();

        $distance = $service->meters(28.4595, 77.0266, 28.4600, 77.0270);

        $this->assertGreaterThan(60, $distance);
        $this->assertLessThan(80, $distance);
    }

    public function test_it_classifies_risk_levels(): void
    {
        $service = new DistanceService();

        $this->assertSame('normal', $service->riskLevel(100));
        $this->assertSame('warning', $service->riskLevel(101));
        $this->assertSame('suspicious', $service->riskLevel(501));
    }
}
