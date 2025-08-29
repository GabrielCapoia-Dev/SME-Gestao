<?php

namespace App\Services;

use Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use App\Models\Setor;
use App\Models\Cargo;
use App\Models\RegimeContratual;
use App\Models\Lotacao;

class GoogleSheetService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Laravel Google Sheets');
        $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->setAccessType('offline');

        $this->service = new Google_Service_Sheets($this->client);
    }

    /**
     * Importa os dados da aba "dados" e salva no banco
     */
    public function importarLotacoes(string $spreadsheetId, string $range = 'dados!A:B')
    {
        $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        $rows = $response->getValues();

        if (empty($rows)) {
            return ['message' => 'Nenhum dado encontrado na planilha.'];
        }

        // Ignorar cabeçalho (primeira linha)
        array_shift($rows);

        foreach ($rows as $row) {
            $codigo = $row[0] ?? null; // Coluna A
            $descricaoLotacao = $row[1] ?? null; // Coluna B (Nome e Setor)

            if (!$codigo || !$descricaoLotacao) {
                continue; // pula linhas inválidas
            }

            // Buscar setor no banco
            $setor = Setor::where('nome', $descricaoLotacao)->first();

            // Criar ou atualizar lotação
            Lotacao::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'nome'       => $descricaoLotacao, // Nome da lotação = descrição da planilha
                    'descricao'  => "Lotação em {$descricaoLotacao}", // Descrição simples
                    'setor_id'   => $setor?->id,
                    'cargo_id'   => null, // deixa cargo em branco para ser preenchido depois
                ]
            );
        }

        return ['message' => 'Importação concluída com sucesso.'];
    }

    public function vincularCargosEmLotacoes(string $spreadsheetId, string $range = 'dados!A:D')
    {
        $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        $rows = $response->getValues();

        if (empty($rows)) {
            return ['message' => 'Nenhum dado encontrado na planilha.'];
        }

        // Ignorar cabeçalho (primeira linha)
        array_shift($rows);

        // Mapeamento de correção de nomes (caso planilha e banco não batam 100%)
        $mapaCargos = [
            'Assessor Especial' => 'Acessor Especial',
            'Auxiliar de Serviços Gerais' => 'Auxiliar de Serviços Gerais e Secretário Escolar',
            'Professor/Secretário Escolar/Auxiliar de Serviços Gerais' => 'Professor',
        ];

        $atualizados = 0;
        $naoEncontrados = [];

        foreach ($rows as $row) {
            $codigo = $row[0] ?? null; // Coluna A
            $cargoNomePlanilha = $row[2] ?? null; // Coluna C
            $regimeNome = $row[3] ?? null; // Coluna D

            if (!$codigo || !$cargoNomePlanilha || !$regimeNome) {
                continue; // pula linhas incompletas
            }

            // Normalizar nome do cargo se precisar
            $cargoNome = $mapaCargos[$cargoNomePlanilha] ?? $cargoNomePlanilha;

            // Buscar regime no banco
            $regime = \App\Models\RegimeContratual::where('nome', trim($regimeNome))->first();
            if (!$regime) {
                $naoEncontrados[] = "Regime não encontrado: {$regimeNome}";
                continue;
            }

            // Buscar cargo no banco (nome + regime)
            $cargo = \App\Models\Cargo::where('nome', trim($cargoNome))
                ->where('regime_contratual_id', $regime->id)
                ->first();

            if (!$cargo) {
                $naoEncontrados[] = "Cargo não encontrado: {$cargoNome} (Regime={$regimeNome})";
                continue;
            }

            // Atualizar lotação vinculando cargo_id
            $lotacao = \App\Models\Lotacao::where('codigo', $codigo)->first();
            if ($lotacao) {
                $lotacao->update(['cargo_id' => $cargo->id]);
                $atualizados++;
            }
        }

        return [
            'message' => "Vinculação concluída: {$atualizados} lotações atualizadas.",
            'nao_encontrados' => $naoEncontrados
        ];
    }
}
