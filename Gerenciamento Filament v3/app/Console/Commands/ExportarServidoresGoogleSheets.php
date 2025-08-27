<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiFilterService;
use App\Services\GoogleSheetService;

class ExportarServidoresGoogleSheets extends Command
{
    protected $signature = 'exportar:servidores-planilha';
    protected $description = 'Exporta os dados dos servidores para a planilha Google Sheets';

    public function handle()
    {
        $apiService = new ApiFilterService();
        $googleSheet = new GoogleSheetService();

        // Chama a função com filtro de local de trabalho
        $dados = $apiService->obterDadosApiServidoresFiltradoPorLocalTrabalho(1, 2025, 'content', 'descricaoLotacao');

        $spreadsheetId = '1bawy7mtk34OVPans34FcJKa8HdH2wHxjW3YGlHPSPOk';
        $range = 'dados!A1';

        $googleSheet->salvarDadosNaPlanilha($dados, $spreadsheetId, $range);

        $this->info('Exportação concluída com sucesso com filtro de local de trabalho!');
    }
}
