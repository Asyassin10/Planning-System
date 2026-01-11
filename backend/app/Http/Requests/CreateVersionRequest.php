<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'resources' => 'sometimes|array',
            'resources.*.resource_id' => 'required|exists:resources,id',
            'resources.*.capacity' => 'required|integer|min:1',
            'resources.*.is_permanent' => 'sometimes|boolean',
            'resources.*.notes' => 'nullable|string',
        ];
    }
}