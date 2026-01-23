<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
class SignedStorageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //Obtenemos el path desde la URL (query param o route param)
        //En este caso lo configuramos como route param en web.php
        $path = $request->route('path');
        //validamos que exista
        if( !Storage::disk('local')->exists($path)){
            abort(404);
        }
        //Forzamos la descarga
        return Storage::disk('local')->download($path);
        //return response()->download(Storage::path($path));
    }
}
