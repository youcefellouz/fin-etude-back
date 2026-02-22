<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
        'guest_name' => $isAuthenticated ? 'nullable|string' : 'nullable|string',
        'guest_phone' => $isAuthenticated ? 'nullable|string' : 'nullable|string',
        'station_id' => 'nullable|exists:stations,id',
        'articles' => 'nullable|array|min:1',
        'articles.*.article_id' => 'required_with:articles|exists:articles,id',
        'articles.*.quantity' => 'required_with:articles|integer|min:1',
        'articles.*.unit_price' => 'nullable|numeric|min:0',
    ];
    }
}
