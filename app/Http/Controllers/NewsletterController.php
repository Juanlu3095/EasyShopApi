<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsletterResource;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Exports\NewslettersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\newslettersubscription;
use App\Mail\clientnewsletter;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newsletters = Newsletter::orderBy('created_at', 'desc')->get();
        return response()->json(NewsletterResource::collection($newsletters), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $newsletter = Newsletter::create($request->all());

        //date_default_timezone_set('Europe/Madrid'); // Con esto ponemos que el timezone sea GMT+2
        $email = $request->email;
        $fecha = date('d-m-Y H:i');

        $datos = array(
            'email' => $email,
            'fecha' => $fecha
        );

        Mail::to('jcooldevelopment@gmail.com')->send(new newslettersubscription($datos)); // Enviamos correo al admin para notificarle de la suscripción.
        Mail::to($email)->send(new clientnewsletter($datos)); // Enviamos correo al admin para notificarle de la suscripción.

        return response()->json([
            'success' => true,
            'data' => new NewsletterResource($newsletter)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $newsletter = Newsletter::find($id);
        return new NewsletterResource($newsletter);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $newsletter = Newsletter::find($id);
        $newsletter->update($request->all()); // Asegurarnos de que $fillable esté bien definido en el modelo
        $newsletter->save();

        return response()->json([
            'success' => true,
            'data' => new NewsletterResource($newsletter)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $newsletter = Newsletter::destroy(explode(",",$idArray));

        if( $newsletter ) {
            return response()->json([
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
            ], 404);
        }
    }

    /**
     * Exports newsletters in xls format for Excel.
     */
    public function export()
    {
        return Excel::download(new NewslettersExport, 'newsletters.xlsx');
    }
}
