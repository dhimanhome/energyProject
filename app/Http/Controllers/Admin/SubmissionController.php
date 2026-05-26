<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Repositories\SubmissionRepository;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request, SubmissionRepository $submissions)
    {
        return view('admin.submissions.index', [
            'submissions' => $submissions->filtered($request->only(['risk_level', 'employee_id', 'site_id', 'from', 'to'])),
        ]);
    }

    public function show(Submission $submission)
    {
        return view('admin.submissions.show', [
            'submission' => $submission->load(['employee', 'site', 'suspiciousLogs']),
        ]);
    }
}
