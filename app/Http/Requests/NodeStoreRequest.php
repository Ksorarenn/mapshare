<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NodeStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // проверка доступа в контроллере/полисе
    }

    public function rules()
    {
        return [
            'node_id'    => ['required', 'string'],
            'content'    => ['nullable', 'string'],
            'links'      => ['nullable', 'json'],
            'image_path' => ['nullable', 'string'],
        ];
    }
}
