<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserupdateRequest extends FormRequest
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

        // Comprobamos que el usuario que realiza la petición sea un cliente
        if($this->user()->role_id !== 3) {
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
            'name' => 'required|string|min:1',
            'email' => 'required|email|min:1',
            'password' => 'required|confirmed|string|min:1'
        ];
    }
}
