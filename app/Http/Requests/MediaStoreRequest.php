<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // проверка доступа будет в контроллере через политику
    }

    public function rules()
    {
        return [
            'node_id' => ['required', 'string'],
            'media'   => ['required', 'file', 'image', 'mimes:jpeg,png,webp', 'max:5120'], // 5 MB = 5120 KB
        ];
    }
}
