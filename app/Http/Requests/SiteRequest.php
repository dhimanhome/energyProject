<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin', 'Supervisor']) ?? false;
    }

    public function rules(): array
    {
        $siteId = $this->route('site')?->id;

        return [
            'site_code' => ['required', 'string', 'max:50', Rule::unique('sites', 'site_code')->ignore($siteId)],
            'site_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'allowed_radius' => ['required', 'integer', 'min:1', 'max:10000'],
            'address' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'employee_ids' => ['array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ];
    }
}
