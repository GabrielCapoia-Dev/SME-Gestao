<?php

namespace App\Services;

use Carbon\Carbon;

class DeclaracaoHoraService
{
    /**
     * Calcula a carga horária em formato HH:mm
     */
    public static function calcularCargaHoraria(?string $inicio, ?string $fim): string
    {
        if (!$inicio || !$fim) {
            return '00:00';
        }

        $horaInicio = Carbon::parse($inicio);
        $horaFim = Carbon::parse($fim);

        if ($horaFim->lessThan($horaInicio)) {
            return '00:00';
        }

        $diff = $horaInicio->diff($horaFim);

        return sprintf('%02d:%02d', $diff->h + ($diff->days * 24), $diff->i);
    }

    /**
     * Verifica se o horário final é anterior ao inicial (retroativo)
     */
    public static function horarioRetroativo(?string $inicio, ?string $fim): bool
    {
        if (!$inicio || !$fim) {
            return false;
        }

        return Carbon::parse($fim)->lessThan(Carbon::parse($inicio));
    }
}
