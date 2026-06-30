<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgressStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // только аутентифицированные пользователи должны попадать сюда (middleware auth:sanctum)
    }

    public function rules()
    {
        return [
            'roadmap_id'      => ['required', 'exists:roadmaps,id'],
            'completed_nodes' => ['required', 'array'],
            'completed_nodes.*'=> ['string'],
        ];
    }
}
