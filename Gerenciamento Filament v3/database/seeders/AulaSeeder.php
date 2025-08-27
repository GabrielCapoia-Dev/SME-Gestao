<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aula;

class AulaSeeder extends Seeder
{
    public function run(): void
    {
        $disciplinas = [
            ['nome' => 'Língua Portuguesa', 'descricao' => 'Leitura, escrita e gramática.'],
            ['nome' => 'Matemática',        'descricao' => 'Aritmética, geometria e resolução de problemas.'],
            ['nome' => 'Ciências',          'descricao' => 'Natureza, corpo humano e experimentos.'],
            ['nome' => 'História',          'descricao' => 'História do Brasil e geral.'],
            ['nome' => 'Geografia',         'descricao' => 'Espaço geográfico e cartografia.'],
            ['nome' => 'Educação Física',   'descricao' => 'Atividades corporais e esportes.'],
            ['nome' => 'Artes',             'descricao' => 'Expressão artística e apreciação.'],
            ['nome' => 'Inglês',            'descricao' => 'Vocabulário e conversação básica.'],
            ['nome' => 'Informática',       'descricao' => 'Letramento digital e noções de tecnologia.'],
            ['nome' => 'Ensino Religioso',  'descricao' => 'Valores, ética e diversidade cultural.'],
        ];

        $this->command?->info('⏳ Iniciando criação/atualização das aulas...');

        $total = count($disciplinas);
        foreach ($disciplinas as $i => $d) {
            Aula::firstOrCreate(
                ['nome' => $d['nome']],
                ['descricao' => $d['descricao']]
            );

            // animação simples com pontos
            $pontos = str_repeat('.', $i % 4); // alterna "", ".", "..", "..."
            $this->command->getOutput()->write("\r⏳ Processando aulas{$pontos} (" . ($i+1) . "/{$total})");
            usleep(150000); // 0.15s só pro efeito ficar visível
        }

        $this->command?->getOutput()->writeln("\r✅ Aulas criadas/atualizadas com sucesso! ({$total})           ");
    }
}
