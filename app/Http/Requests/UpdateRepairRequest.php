<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepairRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status'           => 'sometimes|in:en_attente,en_cours,termine,refuse',
            'estimated_cost'   => 'nullable|numeric|min:0',
            'technician_note'  => 'nullable|string',
            'appointment_date' => 'nullable|date',
        ];
    }
}