<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Turma;
use App\Models\NomeTurma;
use App\Models\SiglaTurma;
use App\Models\Setor;

class TurmaSeeder extends Seeder
{
    private function loading($label, $current, $total)
    {
        $pontos = str_repeat('.', $current % 4); // "", ".", "..", "..."
        $this->command->getOutput()->write("\r⏳ {$label}{$pontos} ({$current}/{$total})");
        usleep(120000); // 0.12s só pra dar efeito
    }

    private function done($label, $total)
    {
        $this->command->getOutput()->writeln("\r✅ {$label} concluído! ({$total})           ");
    }

    public function run(): void
    {
        $nomes   = NomeTurma::all();
        $siglas  = SiglaTurma::all();
        $setores = Setor::all();

        if ($nomes->isEmpty() || $siglas->isEmpty() || $setores->isEmpty()) {
            $this->command->error('É necessário ter NomeTurmas, SiglaTurmas e Setores cadastrados antes de rodar este seeder.');
            return;
        }

        // Pergunta ao usuário quantas turmas criar
        $quantidade = (int) $this->command->ask(
            'Quantas turmas deseja criar?',
            200 // valor padrão
        );

        for ($i = 1; $i <= $quantidade; $i++) {
            $nome  = $nomes->random();
            $sigla = $siglas->random();
            $setor = $setores->random();

            Turma::create([
                'nome_turma_id' => $nome->id,
                'sigla_turma_id' => $sigla->id,
                'setor_id' => $setor->id,
                'descricao' => "Turma {$nome->nome} {$sigla->nome} - {$setor->nome}",
            ]);

            $this->loading('Criando turmas', $i, $quantidade);
        }

        $this->done('Turmas', $quantidade);
    }
}
