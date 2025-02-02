<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Drive;

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
        $client->addScope("https://www.googleapis.com/auth/drive");
        $client->addScope(Drive::DRIVE_READONLY);
        $client->setClientId(config('filesystems.disks.google.clientId'));
        $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $accessToken = $client->fetchAccessTokenWithRefreshToken(config('filesystems.disks.google.refreshToken'));
        $client->setAccessToken($accessToken);

        // Volvemos a obtener el access token con el refresh token si el primero expira
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken(config('filesystems.disks.google.refreshToken'));
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

            /* 'Tamano' => number_format(Storage::fileSize('public/' . $this->ruta_archivo) / 1024, 3), // number_format para establecer hasta 3 decimales
            'Dimensiones' => getimagesize(Storage::path('public/' . $this->ruta_archivo))[0] . ' por ' . getimagesize(Storage::path('public/' . $this->ruta_archivo))[1] . ' píxeles',
            'Tipo' => getimagesize(Storage::path('public/' . $this->ruta_archivo))['mime'],
            'Nombre_archivo' => basename(Storage::path('public/' . $this->ruta_archivo)) */
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
