<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderitemRequest extends FormRequest
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
        // Reglas que se aplican en cualquier caso
        $rules = [
            'producto_id' => 'required|numeric|min:1',
            'subtotal' => 'nullable|numeric',
            'cantidad' => 'required|numeric|min:1'
        ];

        if($this->method() == 'POST') {
            return array_merge($rules, [
                'pedido_id' => 'required|numeric|min:1'
            ]);

        } else {
            return $rules;
        }
    }
}
