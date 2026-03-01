<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'station_id'       => 'required|exists:stations,id',
            'article_id'       => 'required|exists:articles,id',
            'description'      => 'required|string|min:10',
            'warranty'         => 'boolean',
            'appointment_date' => 'nullable|date|after:today',
        ];
    }
}