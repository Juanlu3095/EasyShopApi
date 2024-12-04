<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'nombre' => 'required|string|min:1',
            'apellidos' => 'required|string|min:1',
            'pais' => 'required|string|min:1',
            'direccion' => 'required|string|min:1',
            'codigo_postal' => 'required|numeric|digits:5', // Para España el código postal tiene siempre 5 dígitos
            'poblacion' => 'required|string|min:1',
            'provincia' => 'required|string|min:1',
            'telefono' => 'required|numeric|digits_between:7,15', // El formato recomendado por la UIT indica estos mínimos y máximos de caracteres
            'email' => 'required|email',
            'notas' => 'string|nullable|min:1',
            'metodo_pago' => 'string|required|in:transferencia,tarjeta',
            'nombre_descuento' => 'string|nullable|min:1',
            'tipo_descuento' => 'string|nullable|in:Fijo,Porcentual',
            'descuento' => 'numeric|nullable',
        ];

        // Reglas que se añaden dependiendo del tipo de método
        switch ($this->method()) {
            case "POST": {
                if(!$this->header('X-A-T')) {
                    return array_merge($rules, [
                        'productos' => 'required|array',
                        'subtotal' => 'numeric|required|min:1', // es posible que el POST para añadir pedido en el panel de admin no necesite ni esto ni el total
                        'total' => 'numeric|required|min:1',
                    ]);
                } else { // Para admin
                    return array_merge($rules, [
                        'subtotal' => 'numeric|min:1', // es posible que el POST para añadir pedido en el panel de admin no necesite ni esto ni el total
                        'total' => 'numeric|min:1',
                    ]);
                }
                
            }
            case "PUT": {
                return array_merge($rules, [
                    'estado' => 'numeric|required', // Dudo de si poner el rango de valores que hay en la BD por si se quiere cambiar
                ]);
            }
        }

        

        
    }
}
