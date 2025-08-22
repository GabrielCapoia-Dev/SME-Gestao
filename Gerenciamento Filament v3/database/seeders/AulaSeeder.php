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

        foreach ($disciplinas as $d) {
            Aula::firstOrCreate(
                ['nome' => $d['nome']],
                ['descricao' => $d['descricao']]
            );
        }

        $this->command?->info('Aulas básicas criadas/atualizadas com sucesso.');
    }
}
