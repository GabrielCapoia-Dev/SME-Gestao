<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Turma;
use App\Models\NomeTurma;
use App\Models\SiglaTurma;
use App\Models\Setor;

class TurmaSeeder extends Seeder
{
    public function run(): void
    {
        $nomes = NomeTurma::all();
        $siglas = SiglaTurma::all();
        $setores = Setor::all();

        if ($nomes->isEmpty() || $siglas->isEmpty() || $setores->isEmpty()) {
            $this->command->error('É necessário ter NomeTurmas, SiglaTurmas e Setores cadastrados antes de rodar este seeder.');
            return;
        }

        // Exemplo: criar 200 turmas aleatórias
        for ($i = 0; $i < 200; $i++) {
            $nome = $nomes->random();
            $sigla = $siglas->random();
            $setor = $setores->random();

            Turma::create([
                'nome_turma_id' => $nome->id,
                'sigla_turma_id' => $sigla->id,
                'setor_id' => $setor->id,
                'descricao' => "Turma {$nome->nome} {$sigla->nome} - {$setor->nome}",
            ]);
        }

        $this->command->info('Turmas geradas com sucesso!');
    }
}
