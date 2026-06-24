<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NodeUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content'    => ['sometimes', 'nullable', 'string'],
            'links'      => ['sometimes', 'nullable', 'array'],
            'image_path' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
