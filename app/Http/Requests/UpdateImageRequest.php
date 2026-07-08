<?php

namespace App\Http\Requests;

class UpdateImageRequest extends StoreImageRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // La photo est facultative en modification : l'existante est conservée.
        $rules['photo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'];

        return $rules;
    }
}
