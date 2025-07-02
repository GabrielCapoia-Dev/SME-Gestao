<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atestado;
use App\Models\Servidor;
use App\Models\TipoAtestado;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AtestadoSeeder extends Seeder
{
    public function run()
    {
        $servidores = Servidor::all();
        $tiposAtestado = TipoAtestado::all();

        if ($servidores->isEmpty() || $tiposAtestado->isEmpty()) {
            $this->command->error('Não há servidores ou tipos de atestado cadastrados. Rode os seeders relacionados primeiro.');
            return;
        }

        for ($i = 0; $i < 100; $i++) {
            $servidor = $servidores->random();
            $tipo = $tiposAtestado->random();

            // Data de início aleatória no último ano
            $dataInicio = Carbon::now()->subDays(rand(0, 365));

            // Decide se é prazo indeterminado ou não (10% indeterminado)
            $prazoIndeterminado = rand(1, 10) === 1;

            // Data fim só se não for indeterminado, entre 1 a 30 dias após início
            $dataFim = $prazoIndeterminado ? null : (clone $dataInicio)->addDays(rand(1, 30));

            // CID aleatório (exemplo padrão: letra + 2 números + ponto + número)
            $cid = strtoupper(chr(rand(65, 90))) . rand(10, 99) . '.' . rand(0, 9);

            // 20% chance de ter substituto
            $substituto = null;
            if (rand(1, 5) === 1) {
                // Pega servidor diferente do principal
                $substituto = $servidores->where('id', '!=', $servidor->id)->random()->id ?? null;
            }

            // Quantidade de dias calculada (se não indeterminado)
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
        }
    }
}
