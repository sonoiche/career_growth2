<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSkillsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable','integer','exists:skills,id'],
            'type' => ['nullable','string','max:50'],
            'q' => ['nullable','string','max:100'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:100'],
        ];
    }
    public function authorize(): bool { return true; }
}
