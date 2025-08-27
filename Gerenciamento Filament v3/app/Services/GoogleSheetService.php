<?php

namespace App\Services;

use Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Laravel Google Sheets');
        $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->setAccessType('offline');

        $this->service = new Google_Service_Sheets($this->client);
    }

    public function salvarDadosNaPlanilha(array $dados, string $spreadsheetId, string $range)
    {
        $values = [['Descrição da Lotação']]; // Cabeçalho

        $dadosUnicos = collect($dados)
            ->unique('dados')
            ->sortBy('dados', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        foreach ($dadosUnicos as $dado) {
            $values[] = [$dado['dados']];
        }

        $body = new \Google\Service\Sheets\ValueRange([
            'values' => $values,
        ]);

        $params = ['valueInputOption' => 'RAW'];

        $this->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    }
}
