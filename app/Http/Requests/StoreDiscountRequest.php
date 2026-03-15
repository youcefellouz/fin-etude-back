<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountRequest extends FormRequest
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
            'type' => 'required|in:percentage,valeur',
            'value' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }
    public function messages(): array
    {
        return [
            'type.required' => 'Discount type is required.',
'type.string' => 'Discount type must be a string.',
'type.max' => 'Discount type cannot exceed 255 characters.',
'value.required' => 'Discount value is required.',
'value.numeric' => 'Discount value must be a number.',
'start_date.required' => 'Start date is required.',
'start_date.date' => 'Start date must be a valid date.',
'end_date.required' => 'End date is required.',
'end_date.date' => 'End date must be a valid date.',
'end_date.after_or_equal' => 'End date must be on or after the start date.',
        ];
    }
}
