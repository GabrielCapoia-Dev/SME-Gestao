<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professor;
use App\Models\Servidor;
use App\Models\Turma;
use App\Models\Aula;

class ProfessorSeeder extends Seeder
{
    public function run(): void
    {
        $servidores = Servidor::all();
        $turmas     = Turma::all();
        $aulas      = Aula::all();

        if ($servidores->isEmpty() || $turmas->isEmpty()) {
            $this->command->error('É necessário ter servidores e turmas cadastrados antes de rodar este seeder.');
            return;
        }

        // Vamos transformar 150 servidores aleatórios em Professores
        $quantidade = min(150, $servidores->count());

        $servidoresEscolhidos = $servidores->random($quantidade);

        foreach ($servidoresEscolhidos as $servidor) {
            $turma = $turmas->random();
            $aula  = $aulas->isNotEmpty() ? $aulas->random() : null;

            Professor::create([
                'servidor_id' => $servidor->id,
                'turma_id'    => $turma->id,
                'aula_id'     => $aula?->id,
            ]);
        }

        $this->command->info("Foram criados {$quantidade} professores com turma e aula vinculados.");
    }
}
