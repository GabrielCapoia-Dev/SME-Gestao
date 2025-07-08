<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ServidorApiService
{
    protected string $baseUrl = 'https://servicos.umuarama.pr.gov.br/portaltransparencia-api/api';

    public function obterServidores(int $entidade = 1, int $exercicio = 2025, int $pagina): array
    {
        $response = Http::withHeaders([
            'entidade' => $entidade,
            'exercicio' => $exercicio,
        ])->get("{$this->baseUrl}/servidores", [
            'page' => $pagina,
            'size' => 10000,
            'sort' => [],
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Erro ao acessar a API de servidores: ' . $response->status());
    }
}
