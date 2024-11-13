<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'nombre' => 'required|string',
            'codigo' => 'required|string',
            'tipo' => 'required|string|in:Fijo,Porcentual',
            'descuento' => 'required|numeric',
            'descripcion' => 'string',
            'estado_cupon' => 'required|string|in:Borrador,Publicado',
            'fecha_caducidad' => 'date',
            'gasto_minimo' => 'numeric',
            'limite_uso' => 'numeric'
        ];
    }
}
