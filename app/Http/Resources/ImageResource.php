<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Cache;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Creamos el cliente para autenticarnos en la API de Google Drive
        $client = new Client();
        // $client->addScope("https://www.googleapis.com/auth/drive"); // Permiso para todo en Drive
        $client->addScope(Drive::DRIVE_READONLY);
        $client->setClientId(config('filesystems.disks.google.clientId'));
        $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $accessToken = Cache::get('accessToken');

        // Comprobamos que exista $accessToken en la caché, y si es así se lo pasamos al cliente.
        // Ponemos esto aquí porque si lo ponemos en un else en el if de debajo, entrará en el primer if ya que se cumple $client->isAccessTokenExpired()
        // Tener en cuenta que setAccessToken($accessToken) puede dar error su el token no existe o es null y parar el programa
        if($accessToken) {
            $client->setAccessToken($accessToken);
        }

        // Comprobamos si el token está en caché o si el token del cliente ha expirado
        if (!$accessToken || $client->isAccessTokenExpired()) {
            $newAccessToken = $client->fetchAccessTokenWithRefreshToken(config('filesystems.disks.google.refreshToken'));
            Cache::put('accessToken', $newAccessToken, now()->addMinutes(55));
            $client->setAccessToken($newAccessToken);
        }
        
        $service = new Drive($client);
        $file = $service->files->get($this->ruta_archivo, ['fields' => 'size, imageMediaMetadata, mimeType']); // Como parámetro opcional pasamos lo que necesitamos ver
                                                                                                               // Usar 'fields' => '*' para ver todo
        $tipo = $file->getMimeType();
        $size = $file->getSize();
        $width = $file->getImageMediaMetadata()->getWidth();
        $height = $file->getImageMediaMetadata()->getHeight();

        $arrayData = [
            'Id' => $this->id,
            'Nombre' => $this->nombre,
            'Alt' => $this->alt,
            'Descripcion' => $this->descripcion,
            'Leyenda' => $this->leyenda,
            'Archivo' => $this->ruta_archivo,
            'Fecha' => $this->created_at,
            'Tamano' => number_format($size / 1024, 3),
            'Dimensiones' => $width . ' por ' . $height . ' píxeles',
            'Tipo' => $tipo,
            'Nombre_archivo' => $this->nombre_archivo
        ];

        // Si la imagen está en uso se indica en la propiedad Estado
        if($this->imageable_id) {
            $arrayData['Estado'] = 'Asignada';
        } else {
            $arrayData['Estado'] = 'No asignada';
        }

        return $arrayData;
    }
}
