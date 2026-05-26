<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user()->employee;
        abort_if(! $employee, 403, 'Authenticated user is not linked to an employee profile.');

        return response()->json([
            'data' => $employee->sites()->where('status', 'active')->orderBy('site_name')->get(),
        ]);
    }
}
