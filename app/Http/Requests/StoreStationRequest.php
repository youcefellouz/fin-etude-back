<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255|in:sfax,sousse,tunis',
            'type' => 'required|string|in:commerciale,technique',
            'location' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
        ];
    }
    function messages(): array
    {
       return [
    'name.required'     => 'The name field is required.',
    'city.required'     => 'The city field is required.',
    'type.required'     => 'The type field is required.',
    'location.required' => 'The location field is required.',
];
    }
}
