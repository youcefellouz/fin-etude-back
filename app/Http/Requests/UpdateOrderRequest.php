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
        return [
        'user_id' => 'sometimes|nullable|exists:users,id',
        'guest_name' => 'sometimes|required_without:user_id|string|max:255',
        'guest_phone' => 'sometimes|required_without:user_id|string|max:20',
        'status' => 'sometimes|in:pending,confirmed,cancelled',
        'articles' => 'sometimes|array|min:1',
        'articles.*.article_id' => 'required_with:articles|exists:articles,id',
        'articles.*.quantity' => 'required_with:articles|integer|min:1',
    ];
    }
}
