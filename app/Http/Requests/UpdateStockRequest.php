<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'station_id' => 'sometimes|required|exists:stations,id',
            'article_id' => 'sometimes|required|exists:articles,id',
            'quantity'   => 'sometimes|required|integer|min:0', 
        ];
    }
}