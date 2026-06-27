<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoadmapUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true; // проверка прав будет в политике
    }

    public function rules()
    {
        return [
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'graph_data'  => ['sometimes', 'nullable', 'array'],
        ];
    }
}
