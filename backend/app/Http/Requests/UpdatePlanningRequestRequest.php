<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanningRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'sometimes|array|min:1',
            'items.*.route_id' => 'required|exists:routes,id',
            'items.*.capacity' => 'required|integer|min:1',
            'items.*.start_date' => 'required|date',
            'items.*.end_date' => 'required|date|after_or_equal:items.*.start_date',
        ];
    }
}
