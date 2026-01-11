<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:vehicle,worker',
            'name' => 'required|string|max:255',
            'details' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
