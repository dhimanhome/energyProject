<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin', 'Supervisor', 'Employee']) ?? false;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'active_power' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'voltage' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'current' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'load_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'energy_reading' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'timestamp' => ['nullable', 'date'],
            'meter_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'equipment_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ];
    }
}
