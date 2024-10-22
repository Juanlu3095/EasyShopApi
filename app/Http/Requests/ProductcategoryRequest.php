<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductcategoryRequest extends FormRequest
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
        switch ($this->method()) {
            case "POST": {
                return [
                    "nombre" => "required|unique:productcategories",
                    "slug" => "required|unique:productcategories",
                ];
            }
            case "PUT": {
                return [
                    "nombre" => "required|unique:productcategories,nombre," . $this->route('productcategory'),
                    "slug" => "required|unique:productcategories,slug," . $this->route('productcategory'),
                ];
            }
        }

        return [
            'nombre' => 'required',
            'slug' => 'required'
        ];
    }
}
