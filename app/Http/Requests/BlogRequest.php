<?php

namespace App\Http\Requests;

use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:7048',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',
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
        return $validatedData;
    }
}
