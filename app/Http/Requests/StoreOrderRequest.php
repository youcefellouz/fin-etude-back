<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
       $isAuthenticated = auth('sanctum')->check();

    return [
        'status' => 'nullable|in:pending,confirmed,cancelled',
        'guest_name' => $isAuthenticated ? 'nullable|string' : 'required|string',
        'guest_phone' => $isAuthenticated ? 'nullable|string' : 'required|string',
        'station_id' => 'required|exists:stations,id',
        'articles' => 'required|array|min:1',
        'articles.*.article_id' => 'required|exists:articles,id',
        'articles.*.quantity' => 'required|integer|min:1',
        'articles.*.unit_price' => 'nullable|numeric|min:0',
    ];
    
    }
    public function messages(): array
    {
        return [
            'station_id.required' => 'Le point de vente est requis.',
            'station_id.exists' => 'Le point de vente spécifié n\'existe pas.',
            'status.required' => 'Le champ statut est requis.',
            'status.string' => 'Le champ statut doit être une chaîne de caractères.',
            'guest_name.required_without' => 'Le champ nom du client est requis lorsque user_id n\'est pas fourni.',
            'guest_name.string' => 'Le champ nom du client doit être une chaîne de caractères.',
            'guest_phone.required_without' => 'Le champ téléphone du client est requis lorsque user_id n\'est pas fourni.',
            'guest_phone.string' => 'Le champ téléphone du client doit être une chaîne de caractères.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
        ];
    }
}
