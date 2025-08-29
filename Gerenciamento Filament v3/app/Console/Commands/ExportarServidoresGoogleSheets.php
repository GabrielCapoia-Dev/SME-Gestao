<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;

class ImportarLotacoesGoogleSheets extends Command
{
    protected $signature = 'lotacoes-planilha';
    protected $description = 'Importa os dados de lotações da planilha Google Sheets e salva no banco';

    public function handle()
    {
        $googleSheet = new GoogleSheetService();

        // ID da planilha (o mesmo que você usou)
        $spreadsheetId = '1bawy7mtk34OVPans34FcJKa8HdH2wHxjW3YGlHPSPOk';

        try {
            $resultado = $googleSheet->importarLotacoes($spreadsheetId, 'dados!A:D');

            $this->info($resultado['message'] ?? 'Importação concluída!');
        } catch (\Exception $e) {
            $this->error('Erro durante a importação: ' . $e->getMessage());
        }
    }
}
