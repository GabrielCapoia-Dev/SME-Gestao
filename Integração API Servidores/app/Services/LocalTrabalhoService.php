<?php

namespace App\Services;

use App\Models\Setor;
use App\Services\ServidorApiService;

class LocalTrabalhoService
{
    public function obterLocalTrabalho(): array
    {
        $service = new ServidorApiService();

        $dados = [];

        try {
            for ($pagina = 0; $pagina < 3; $pagina++) {
                $servidores = $service->obterServidores(1, 2025, $pagina);

                foreach ($servidores['content'] as $servidor) {
                    $dados[] = [
                        'local_trabalho' => $servidor['localTrabalho'] ?? 'N/A',
                    ];
                }
            }

            $dadosOrdenados = collect($dados)
                ->unique('local_trabalho')
                ->sortBy('local_trabalho', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->toArray();

            return $dadosOrdenados;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    public function salvarLocalTrabalho(array $dados)
    {
        foreach ($dados as $item) {
            $nome = trim($item['local_trabalho']);

            if ($nome === '' || $nome === 'N/A') {
                continue; // Ignorar valores inválidos
            }

            // Usa updateOrCreate para evitar duplicatas
            Setor::updateOrCreate(
                ['nome' => $nome],  // chave única
                ['nome' => $nome]   // dados a salvar
            );
        }

        return response()->json(['success' => true, 'count' => count($dados)], 200); // 200 OK['success' => true, 'count' => count($dados)];
    }
}
