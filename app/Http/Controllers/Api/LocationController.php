<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationUpdateRequest;
use App\Models\LocationUpdate;

class LocationController extends Controller
{
    public function update(LocationUpdateRequest $request)
    {
        $employee = $request->user()->employee;
        abort_if(! $employee, 403, 'Authenticated user is not linked to an employee profile.');

        $location = LocationUpdate::create([
            'employee_id' => $employee->id,
            'latitude' => $request->validated('latitude'),
            'longitude' => $request->validated('longitude'),
            'accuracy' => $request->validated('accuracy'),
            'recorded_at' => $request->validated('recorded_at') ?? now(),
        ]);

        $employee->forceFill(['last_seen' => now()])->save();
        $request->user()->forceFill(['last_seen_at' => now()])->save();

        return response()->json(['message' => 'Location updated.', 'data' => $location], 201);
    }
}
