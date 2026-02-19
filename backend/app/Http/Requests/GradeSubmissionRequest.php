<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'marks' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'marks.required' => 'Marks is required.',
            'marks.numeric' => 'Marks must be a number.',
            'marks.min' => 'Marks cannot be less than 0.',
            'marks.max' => 'Marks cannot be higher than 100'
        ];
    }
}
