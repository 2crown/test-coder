<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'sometimes|required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:admin,teacher,student,parent',
            'admission_number' => 'nullable|string|unique:students,admission_number,' . $userId . ',user_id',
            'employee_id' => 'nullable|string|unique:teachers,employee_id,' . $userId . ',user_id',
            'class_id' => 'nullable|exists:classes,id',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'occupation' => 'nullable|string|max:255',
            'workplace' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email has already been taken.',
            'password.min' => 'The password must be at least 8 characters.',
            'role.required' => 'The role field is required.',
            'role.in' => 'The selected role is invalid.',
        ];
    }
}
