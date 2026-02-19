<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|in:assignment,test,exam',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'nullable|exists:terms,id',
            'total_marks' => 'required|integer|min:1|max:1000',
            'due_date' => 'nullable|date|after:now',
            'description' => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The assessment title is required.',
            'type.required' => 'The assessment type is required.',
            'type.in' => 'The assessment type must be assignment, test, or exam.',
            'subject_id.required' => 'The subject is required.',
            'class_id.required' => 'The class is required.',
            'total_marks.required' => 'Total marks is required.',
            'total_marks.integer' => 'Total marks must be a number.',
            'due_date.after' => 'Due date must be in the future.',
        ];
    }
}
