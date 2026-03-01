<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'article_id' => 'required|exists:articles,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'article_id.required' => 'Article is required.',
            'article_id.exists'   => 'This article does not exist.',
            'rating.required'     => 'Rating is required.',
            'rating.integer'      => 'Rating must be a number.',
            'rating.min'          => 'Rating must be at least 1.',
            'rating.max'          => 'Rating must not exceed 5.',
            'comment.max'         => 'Comment must not exceed 500 characters.',
        ];
    }
}