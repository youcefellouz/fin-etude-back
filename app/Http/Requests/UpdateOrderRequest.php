<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'                    => 'nullable|in:pending,confirmed,cancelled',
            'guest_name'                => 'nullable|string',
            'guest_phone'               => 'nullable|string',
            'articles'                  => 'nullable|array|min:1',
            'articles.*.article_id'     => 'required_with:articles|exists:articles,id',
            'articles.*.quantity'       => 'required_with:articles|integer|min:1',
        ];
    }
}
