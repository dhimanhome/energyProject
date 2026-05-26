<?php

namespace App\Http\Controllers\Api;

use App\Actions\StoreReadingSubmission;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;

class SubmissionController extends Controller
{
    public function store(StoreSubmissionRequest $request, StoreReadingSubmission $storeReadingSubmission)
    {
        $employeeId = $request->user()->employee?->id;
        $data = $request->validated();
        $data['employee_id'] = $employeeId ?: ($data['employee_id'] ?? null);

        abort_if(! $data['employee_id'], 403, 'Authenticated user is not linked to an employee profile.');

        $submission = $storeReadingSubmission->handle(
            $data,
            $request->file('meter_photo'),
            $request->file('equipment_photo'),
        );

        return response()->json([
            'message' => 'Submission accepted for audit.',
            'data' => $submission,
        ], 201);
    }
}
