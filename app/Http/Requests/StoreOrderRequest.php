<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isAuthenticated = auth('sanctum')->check();

        return [
            'status'                   => 'nullable|in:pending,confirmed,cancelled',
            'guest_name'               => $isAuthenticated ? 'nullable|string' : 'required|string',
            'guest_phone'              => $isAuthenticated ? 'nullable|string' : 'required|string',
            'articles'                 => 'required|array|min:1',
            'articles.*.article_id'    => 'required|exists:articles,id',
            'articles.*.quantity'      => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'guest_name.required'          => 'le nom est requis pour les visiteurs.',
            'guest_phone.required'         => 'le numéro de téléphone est requis pour les visiteurs.',
            'articles.required'            => 'il faut ajouter au moins un article.',
            'articles.*.article_id.exists' => 'l\'article n\'existe pas.',
            'articles.*.quantity.min'      => 'la quantité doit être au minimum 1.',
        ];
    }
}