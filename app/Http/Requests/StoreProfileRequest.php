<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            //'user_id' => 'required|exists:users,id',
            'date_birth' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    function messages(): array
    {
        return [
            'user_id.required' => 'The user_id field is required.',
            'user_id.exists' => 'The specified user_id does not exist.',
            'phone.string' => 'The phone field must be a string.',
            'phone.max' => 'The phone field may not be greater than 20 characters.',
            'address.string' => 'The address field must be a string.',
            'address.max' => 'The address field may not be greater than 255 characters.',
            'date_birth.date' => 'The date_birth field must be a valid date.',
            'image.image' => 'The image field must be an image file.',
            'image.max' => 'The image field may not be greater than 2048 kilobytes.',
        ];
    }
}
