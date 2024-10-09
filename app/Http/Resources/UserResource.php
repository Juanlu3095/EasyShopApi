<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Id' => $this->id,
            'Nombre' => $this->name,
            'Email' => $this->email,
            'Role_id' => $this->role_id,
            'Rol' => $this->role->nombre,
            'Último_acceso' => optional(User::find($this->id)->tokens->last())->last_used_at ? User::find($this->id)->tokens->last()->last_used_at->format('d-m-Y G:i') : null,
        ];
        // Se usa optional para no recibir error en caso de que no exista token de usuario. Por ello comprobamos que exista con un operador ternario (?)
        // Al usarse la función ValidarTokenA cada vez que nos movemos por el dashboard desde Angular, la actualización de la fecha de acceso es muy fiable
    }
}
