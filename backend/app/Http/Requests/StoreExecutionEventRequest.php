<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExecutionEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function
    rules(): array
    {
        return [
            'operational_plan_version_id' => 'required|exists:operational_plan_versions,id',
            'event_type' => 'required|string|max:255',
            'event_data' => 'nullable|array',
            'recorded_at' => 'sometimes|date',
        ];
    }
}