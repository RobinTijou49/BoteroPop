<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:256'],
            'prix' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'shopify_variant_id' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:bp_tags,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'description' => 'description',
            'prix' => 'prix',
            'photo' => 'photo',
            'adresse' => 'adresse',
            'shopify_variant_id' => 'Shopify Variant ID',
            'tags' => 'tags',
        ];
    }
}
