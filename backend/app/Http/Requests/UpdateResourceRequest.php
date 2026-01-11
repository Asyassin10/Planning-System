<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'sometimes|required|in:vehicle,worker',
            'name' => 'sometimes|required|string|max:255',
            'details' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
