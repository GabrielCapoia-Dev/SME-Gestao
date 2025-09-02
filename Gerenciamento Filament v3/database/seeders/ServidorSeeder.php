<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servidor;
use App\Services\ServidorApiService;
use App\Services\ApiFilterService;

class ServidorSeeder extends Seeder
{
    public function run(): void
    {
        $service = new ServidorApiService();
        $filter  = new ApiFilterService();

        $entidade = 1;
        $exercicio = 2025;
        $paginas = 3;

        $locaisProibidos = $filter->locaisTrabalhoIndesejados();

        $total = 0;

        for ($pagina = 0; $pagina < $paginas; $pagina++) {
            $dados = $service->obterServidores($entidade, $exercicio, $pagina);

            foreach ($dados['content'] as $servidor) {
                $local = $servidor['localTrabalho'] ?? 'N/A';

                // pula servidores com local proibido
                if (in_array($local, $locaisProibidos)) {
                    continue;
                }

                Servidor::updateOrCreate(
                    ['matricula' => $servidor['matricula']], // evita duplicatas
                    [
                        'nome'          => $servidor['nome'],
                        'data_admissao' => $servidor['dataAdmissao'],
                    ]
                );
                $total++;
            }
        }

        $this->command->info("✅ {$total} servidores importados da API (aplicando filtro de local de trabalho)");
    }
}
