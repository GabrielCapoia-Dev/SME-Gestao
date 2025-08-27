<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professor;
use App\Models\Servidor;
use App\Models\Turma;
use App\Models\Aula;

class ProfessorSeeder extends Seeder
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
        $servidores = Servidor::all();
        $turmas     = Turma::all();
        $aulas      = Aula::all();

        if ($servidores->isEmpty() || $turmas->isEmpty()) {
            $this->command->error('É necessário ter servidores e turmas cadastrados antes de rodar este seeder.');
            return;
        }

        // Pergunta no terminal
        $quantidadeDesejada = (int) $this->command->ask(
            'Quantos professores deseja criar?',
            150 // valor padrão
        );

        // Garante que não escolha mais do que o número de servidores disponíveis
        $quantidade = min($quantidadeDesejada, $servidores->count());

        $servidoresEscolhidos = $servidores->random($quantidade);

        $i = 0;
        foreach ($servidoresEscolhidos as $servidor) {
            $turma = $turmas->random();
            $aula  = $aulas->isNotEmpty() ? $aulas->random() : null;

            Professor::create([
                'servidor_id' => $servidor->id,
                'turma_id'    => $turma->id,
                'aula_id'     => $aula?->id,
            ]);

            $this->loading('Criando professores', ++$i, $quantidade);
        }

        $this->done('Professores', $quantidade);
    }
}
