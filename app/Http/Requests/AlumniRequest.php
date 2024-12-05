<?php

namespace App\Http\Requests;

use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class AlumniRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:students,email',
            'year' => 'nullable|string|max:10',
            'course' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'password' => 'max:20,min:5',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation(Validator $validator)
    {
        Helper::sendError('validation error', $validator->errors());
    }


    /**
     * Return validated data as role_id.
     */
    public function validated($key = null, $default = null)
    {
        $validatedData = parent::validated();
        if ($validatedData['password']) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            $validatedData['password'] = Hash::make("12345678");
        }
        return $validatedData;
    }
}
