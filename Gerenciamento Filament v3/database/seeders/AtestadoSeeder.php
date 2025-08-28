<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atestado;
use App\Models\Servidor;
use App\Models\TipoAtestado;
use Carbon\Carbon;

class AtestadoSeeder extends Seeder
{
    public function run()
    {
        $this->command?->info('Iniciando processo de criação de atestados...');

        $servidores = Servidor::all();
        $tiposAtestado = TipoAtestado::all();

        if ($servidores->isEmpty() || $tiposAtestado->isEmpty()) {
            $this->command->error('Não há servidores ou tipos de atestado cadastrados. Rode os seeders relacionados primeiro.');
            return;
        }

        $total = (int) $this->command->ask(
            'Quantos atestados deseja criar?',
            500
        );

        for ($i = 0; $i < $total; $i++) {
            $servidor = $servidores->random();
            $tipo = $tiposAtestado->random();

            $dataInicio = Carbon::now()->subDays(rand(0, 365));
            $prazoIndeterminado = rand(1, 10) === 1;
            $dataFim = $prazoIndeterminado ? null : (clone $dataInicio)->addDays(rand(1, 30));

            $cid = strtoupper(chr(rand(65, 90))) . rand(10, 99) . '.' . rand(0, 9);

            $substituto = null;
            if (rand(1, 5) === 1) {
                $substituto = $servidores->where('id', '!=', $servidor->id)->random()->id ?? null;
            }

            $quantidadeDias = $prazoIndeterminado ? null : ($dataInicio->diffInDays($dataFim) + 1);

            Atestado::create([
                'servidor_id' => $servidor->id,
                'tipo_atestado_id' => $tipo->id,
                'data_inicio' => $dataInicio->format('Y-m-d'),
                'data_fim' => $dataFim ? $dataFim->format('Y-m-d') : null,
                'cid' => $cid,
                'prazo_indeterminado' => $prazoIndeterminado,
                'substituto_id' => $substituto,
                'quantidade_dias' => $quantidadeDias,
                'created_at' => Carbon::now()->subDays(rand(0, 365)),
                'updated_at' => Carbon::now()->subDays(rand(0, 365)),
            ]);

            // animação simples: mostra progresso com pontos
            $pontos = str_repeat('.', ($i % 4)); // alterna entre "", ".", "..", "..."
            $this->command->getOutput()->write("\r⏳ Processando atestados{$pontos} ({$i}/{$total})");
            usleep(100000); // 0.1s só pra ver o efeito
        }

        $this->command?->getOutput()->writeln("\r✅ Atestados criados com sucesso! ({$total})            ");
    }
}
