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
            'city' => 'required|string|max:255',
            'type' => 'required|string|in:commerciale,technique',
            'location' => 'required|string|max:255',
        ];
    }
    function messages(): array
    {
        return [
            'name.required' => 'Le champ nom est requis.',
            'city.required' => 'Le champ ville est requis.',
            'type.required' => 'Le champ type est requis.',
            'location.required' => 'Le champ localisation est requis.',
        ];
    }
}
