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
            'guest_name'               => $isAuthenticated ? 'nullable|string' : 'required|string|max:100',
            'guest_phone'              => $isAuthenticated ? 'nullable|string' : 'required|string|max:20',
            'city'                     => 'required|string|in:tunis,sousse,sfax,bizerte,gabes,monastir,nabeul,tozeur,kairouan,kasserine,guelbes,jendouba,kef,mahdia,medenine,manouba,zaghouan,siliana,ariana,beja,ben arous,sidi bouzid,tataouine,test',
            'address'                  => 'required|string|max:255',
            'payment_method'           => 'required|in:cash,card',
            'articles'                 => 'required|array|min:1',
            'articles.*.article_id'    => 'required|exists:articles,id',
            'articles.*.quantity'      => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'guest_name.required'          => 'name is required for guests',
            'guest_phone.required'         => 'phone number is required for guests',
            'city.required'                => 'city is required',
            'city.in'                      => 'city must be a valid Tunisian governorate',
            'address.required'             => 'address is required',
            'payment_method.required'      => 'payment method is required',
            'payment_method.in'            => 'payment method must be cash or card',
            'articles.required'            => 'you must add at least one article',
            'articles.*.article_id.exists' => 'article does not exist',
            'articles.*.quantity.min'      => 'quantity must be at least 1',
        ];
    }
}