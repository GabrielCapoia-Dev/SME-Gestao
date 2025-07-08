<?php

namespace App\Http\Controllers;

use App\Services\ServidorApiService;
use Illuminate\Http\JsonResponse;

class ServidorController extends Controller
{
    public function index(ServidorApiService $service): JsonResponse
    {
        try {
            for ($pagina = 0; $pagina < 3; $pagina++) {
                $dados = $service->obterServidores(1, 2024, $pagina);
            }
            return response()->json($dados);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
