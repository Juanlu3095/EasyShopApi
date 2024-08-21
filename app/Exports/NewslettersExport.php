<?php

namespace App\Exports;

use App\Http\Resources\NewsletterResource;
use App\Models\Newsletter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewslettersExport implements FromCollection,WithHeadings
{
    /**
    * Permite cambiar el nombre de las cabeceras para el Excel.
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array 
    {
        return [
            'Id',
            'Email',
            'Fecha',
        ];
    }
    public function collection()
    {
        $newsletters = Newsletter::all();
        
        $newsletters = $newsletters->map(function ($newsletter) {
            return [
                'id' => $newsletter->id,
                'email' => $newsletter->email,
                'created_at' => $newsletter->created_at->format('d-m-Y'), // Cambiamos el formato de la fecha
            ];
        });

        return $newsletters;
        
    }
}