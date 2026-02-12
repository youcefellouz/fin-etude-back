<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
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
            'station_id' => 'required|exists:stations,id',
            'article_id' => 'required|exists:articles,id',
            'quantity' => [
            function ($attribute, $value, $fail) {
                $stationId = $this->input('station_id');
                if ($stationId) {
                    $station = \App\Models\Station::find($stationId);
                    if ($station && $station->type !== 'commerciale' && !is_null($value)) {
                        $fail('La quantité ne doit pas être remplie pour cette station.');
                    }
                    if ($station && $station->type === 'commerciale' && (is_null($value) || !is_numeric($value))) {
                        $fail('La quantité est requise et doit être un nombre pour une station commerciale.');
                    }
                }
            }
        ],
        ];
    }
    function messages(): array
    {
        return [
            'station_id.required' => 'Le champ station_id est requis.',
            'station_id.exists' => 'Le station_id doit exister dans la table stations.',
            'article_id.required' => 'Le champ article_id est requis.',
            'article_id.exists' => 'Le article_id doit exister dans la table articles.',
            'quantity.required' => 'Le champ quantity est requis.',
            'quantity.numeric' => 'Le champ quantity doit être un nombre.',
        ];
    }
}
