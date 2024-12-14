<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderitemclientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Comprobamos que el solicitante es un usuario con cuenta en la aplicación
        if(!$this->user()->id) {
            return false;
        }

        // Comprobamos que el usuario que realiza la petición coincide con el user_id del pedido
        if($this->user()->id !== Order::find($this->idPedido)->user_id) {
            return false;
        }

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
            'idPedido' => 'required|numeric'
        ];
    }
}
