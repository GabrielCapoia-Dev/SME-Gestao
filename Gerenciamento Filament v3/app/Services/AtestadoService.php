<?php

namespace App\Services;

use Carbon\Carbon;

class AtestadoService
{
    /**
     * Calcula a quantidade de dias entre duas datas,
     * ou retorna "Prazo indeterminado" se for o caso.
     *
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @param bool $prazoIndeterminado
     * @return string
     */
    public static function calcularQuantidadeDias(?string $dataInicio, ?string $dataFim, bool $prazoIndeterminado): string
    {
        if ($prazoIndeterminado) {
            return 'Prazo indeterminado';
        }

        if (!$dataInicio || !$dataFim) {
            return '0';
        }

        $inicio = Carbon::parse($dataInicio)->startOfDay();
        $fim = Carbon::parse($dataFim)->startOfDay();

        $dias = $inicio->diffInDays($fim) + 1;

        return (string) $dias;
    }

    public static function validarDataRetroativa(int $dataInicio, int $dataFim): bool
    {
        if ($dataInicio > $dataFim) {
            return true;
        }

        return false;
    }
}
