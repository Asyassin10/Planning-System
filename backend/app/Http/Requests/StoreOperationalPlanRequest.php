<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOperationalPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'planning_request_item_id' => 'required|exists:planning_request_items,id',
            'version' => 'required|array',
            'version.valid_from' => 'required|date',
            'version.valid_to' => 'required|date|after_or_equal:version.valid_from',
            'version.notes' => 'nullable|string',
            'version.resources' => 'sometimes|array',
            'version.resources.*.resource_id' => 'required|exists:resources,id',
            'version.resources.*.capacity' => 'required|integer|min:1',
            'version.resources.*.is_permanent' => 'sometimes|boolean',
            'version.resources.*.notes' => 'nullable|string',
        ];
    }
}
