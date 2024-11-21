<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentmethodRequest extends FormRequest
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
            'nombre' => 'required|string|min:1',
            'activo' => 'required|numeric|in:1,2',
            'descripcion' => 'required|string|min:1',
            'descripcion_cliente' => 'required|string|min:1',
            'configuracion' => 'required|json'
        ];
    }
}
