<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoadmapStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // только аутентифицированные пользователи могут создавать
    }

    public function rules()
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'graph_data'  => ['nullable', 'json'],
        ];
    }
}
