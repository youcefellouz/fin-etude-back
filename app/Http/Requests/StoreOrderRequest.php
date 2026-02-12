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
       return [
        'status' => 'nullable|in:pending,confirmed,cancelled',
        'guest_name' => 'required_without:user_id|string',
        'guest_phone' => 'required_without:user_id|string',
        'user_id' => 'nullable|exists:users,id',
        
        // articles array
        'articles' => 'required|array|min:1',
        'articles.*.article_id' => 'required|exists:articles,id',
        'articles.*.quantity' => 'required|integer|min:1',
        // إضافة unit_price اختياري - إذا لم يُرسل سيحسب تلقائياً
        'articles.*.unit_price' => 'nullable|numeric|min:0',
    ];
    }
    public function messages(): array
    {
        return [
            'global_price.required' => 'Le champ prix global est requis.',
            'global_price.numeric' => 'Le champ prix global doit être un nombre.',
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
