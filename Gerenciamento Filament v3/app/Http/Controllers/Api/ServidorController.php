<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servidor;
use Illuminate\Http\Request;

class ServidorController extends Controller
{
    public function index()
    {
        return response()->json(Servidor::all());
    }

    public function show($matricula)
    {
        $servidor = Servidor::where('matricula', $matricula)->first();

        if (!$servidor) {
            return response()->json(['error' => 'Servidor não encontrado.'], 404);
        }

        return response()->json($servidor);
    }
}
