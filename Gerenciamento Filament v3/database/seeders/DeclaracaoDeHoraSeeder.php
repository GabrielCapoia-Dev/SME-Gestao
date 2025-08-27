<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeclaracaoDeHora;
use App\Models\Servidor;
use App\Models\Turno;
use Carbon\Carbon;

class DeclaracaoDeHoraSeeder extends Seeder
{
    private function loading($label, $current, $total)
    {
        $pontos = str_repeat('.', $current % 4); // "", ".", "..", "..."
        $this->command->getOutput()->write("\r⏳ {$label}{$pontos} ({$current}/{$total})");
        usleep(120000); // 0.12s pra dar efeito
    }

    private function done($label, $total)
    {
        $this->command->getOutput()->writeln("\r✅ {$label} concluído! ({$total})           ");
    }

    public function run(): void
    {
        $servidores = Servidor::all();
        $turnos = Turno::all();

        if ($servidores->isEmpty()) {
            $this->command->error('Não há servidores cadastrados. Rode os seeders de servidores primeiro.');
            return;
        }

        if ($turnos->isEmpty()) {
            $this->command->error('Não há turnos cadastrados. Rode o seeder de turnos primeiro.');
            return;
        }

        // Pergunta no terminal
        $qtdDeclaracoes = (int) $this->command->ask(
            'Quantas declarações de hora deseja criar?',
            500 // padrão se apertar Enter
        );

        for ($i = 1; $i <= $qtdDeclaracoes; $i++) {
            $servidor = $servidores->random();
            $turno = $turnos->random();

            // Data aleatória no último ano
            $data = Carbon::now()->subDays(rand(0, 365))->format('Y-m-d');

            // Definir hora início/fim conforme turno
            switch ($turno->nome) {
                case 'Integral':
                    $horaInicio = '08:00';
                    $horaFim = '17:00';
                    break;
                case 'Manhã':
                    $horaInicio = '08:00';
                    $horaFim = '12:00';
                    break;
                case 'Tarde':
                    $horaInicio = '13:30';
                    $horaFim = '17:00';
                    break;
                case 'Noite':
                    $horaInicio = '18:00';
                    $horaFim = '22:00';
                    break;
                default:
                    $horaInicio = '08:00';
                    $horaFim = '11:00';
                    break;
            }

            // Carga horária em horas
            $cargaHoraria = Carbon::parse($horaInicio)->diffInHours(Carbon::parse($horaFim)) . 'h';

            DeclaracaoDeHora::create([
                'servidor_id'   => $servidor->id,
                'turno_id'      => $turno->id,
                'data'          => $data,
                'hora_inicio'   => $horaInicio,
                'hora_fim'      => $horaFim,
                'cid'           => rand(0, 1) ? strtoupper(chr(rand(65, 90))) . rand(10, 99) . '.' . rand(0, 9) : null,
                'carga_horaria' => $cargaHoraria,
                'created_at'    => Carbon::now()->subDays(rand(0, 365)),
                'updated_at'    => Carbon::now()->subDays(rand(0, 365)),
            ]);

            $this->loading('Criando declarações de hora', $i, $qtdDeclaracoes);
        }

        $this->done('Declarações de hora', $qtdDeclaracoes);
    }
}
