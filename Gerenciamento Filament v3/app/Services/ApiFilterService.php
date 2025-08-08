<?php

namespace App\Services;

use App\Models\Setor;
use App\Services\ServidorApiService;
use Dotenv\Util\Str;

class ApiFilterService
{
    public function obterDadosApiServidores(String $entidade, String $exercicio, String $content, String $param,): array
    {
        $service = new ServidorApiService();

        $dados = [];

        try {
            for ($pagina = 0; $pagina < 3; $pagina++) {
                $servidores = $service->obterServidores($entidade, $exercicio, $pagina);

                foreach ($servidores[$content] as $servidor) {
                    $dados[] = [
                        'dados' => $servidor[$param] ?? 'N/A',
                    ];
                }
            }

            $dadosOrdenados = collect($dados)
                ->unique('dados')
                ->sortBy('dados', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->toArray();

            return $dadosOrdenados;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    public function salvarLocalTrabalho()
    {
        $dados = $this->obterLocalTrabalho();

        foreach ($dados as $item) {
            $nome = trim($item['local_trabalho']);

            if ($nome === '' || $nome === 'N/A') {
                continue; // Ignorar valores inválidos
            }

            // Usa updateOrCreate para evitar duplicatas
            Setor::updateOrCreate(
                ['nome' => $nome],  // chave única
                ['nome' => $nome]   // dados a salvar
            );
        }

        return response()->json(['success' => true, 'count' => count($dados)], 200); // 200 OK['success' => true, 'count' => count($dados)];
    }


    public function obterLocalTrabalho(): array
    {
        $service = new ServidorApiService();

        $dados = [];

        try {
            for ($pagina = 0; $pagina < 3; $pagina++) {
                $servidores = $service->obterServidores(1, 2025, $pagina);

                foreach ($servidores['content'] as $servidor) {
                    $dados[] = [
                        'local_trabalho' => $servidor['localTrabalho'] ?? 'N/A',
                    ];
                }
            }

            $dadosOrdenados = collect($dados)
                ->unique('local_trabalho')
                ->filter(function ($item) {
                    return !in_array($item['local_trabalho'], [
                        "(EM DESUSO).",
                        "6º SUBGRUPAMENTO DE BOMBEIROS INDEPENDENTES DE UMUARAMA",
                        "ACESF - Administração de Cemitérios e Serviços Funerários",
                        "ADMINISTRAÇÃO- PRÉDIO",
                        "AEROPORTO MUNICIPAL DE UMUARAMA",
                        "AGRICULTURA  - PRÉDIO",
                        "AGÊNCIA DE ATENDIMENTO DO TRABALHO E EMPREGO EM UMUARAMA",
                        "AMBULATORIO MUNICIPAL DE INFECTOLOGIA",
                        "ASSISTENCIA SOCIAL - CENTRO POP",
                        "ASSISTÊNCIA EM SAÚDE",
                        "ATERRO SANITÁRIO",
                        "ATOS OFICIAIS - PRÉDIO",
                        "BANCO DE ALIMENTOS",
                        "CANTINA -  PRÉDIO",
                        "CASA DO EMPREENDEDOR",
                        "CASA DOS CONSELHOS",
                        "CEM - CENTRO DE ESPECIALIDADES MÉDICAS",
                        "CENTRAL FARMACEUTICA",
                        "CENTRO DE EVENTOS PREFEITO ALEXANDRE CERANTO",
                        "CEO - CENTRO DE ESPECIALIDADES ODONTOLÓGICAS",
                        "CONSELHO TUTELAR",
                        "CONTABILIDADE - PRÉDIO",
                        "CONTROLE INTERNO - PRÉDIO",
                        "CENTRAL DE CADASTRO ÚNICO",
                        "CRAM - CENTRO DE REFERÊNCIA EM ATENDIMENTO A MULHER",
                        "CRAS 1",
                        "CRAS 2",
                        "CRAS 3",
                        "CREAS - CENTRO DE REFERÊNCIA ESPECIALIZADO DE ASSISTÊNCIA",
                        "CREAS 2",
                        "CRMI - CENTRO DE REFERENCIA MATERNO INFANTIL",
                        "DIRETORIA DE ARRECADAÇÃO E FISCALIZAÇÃO",
                        "DIRETORIA DE COMPRAS E ALMOXARIFADO",
                        "DIRETORIA DE HABITACAO",
                        "DIRETORIA DE RECURSOS HUMANOS - PRÉDIO",
                        "DIRETORIA DE SERVIÇOS PÚBLICOS",
                        "DIVIDA ATIVA - PRÉDIO",
                        "DIVISÃO DE ARBORIZAÇÃO",
                        "DIVISÃO DE CONTROLE DA PRODUÇÃO AGROPECUÁRIA E DFC",
                        "DIVISÃO DE FISCALIZAÇÃO E RECEITAS MOBILIÁRIAS",
                        "DIVISÃO DE CONTROLE DE DÍVIDA ATIVA",
                        "DIVISÃO DE MANUTENÇÃO DOS PROPRIOS MUNICIPAIS",
                        "DIVISÃO DE POSTURA",
                        "DIVISÃO DE RECEITAS IMOB., CAD. IMOB. E GEORREFERENCIAMENTO",
                        "DIVISÃO RECEITAS IMOBILIÁRIAS",
                        "FISCAIS",
                        "FUNDAÇÃO CULTURAL DE UMUARAMA",
                        "FUNDO DE PREVIDENCIA -  FPMU",
                        "FUNDO MUNICIPAL DE SAÚDE",
                        "FÓRUM ESTADUAL",
                        "GABINETE DO PREFEITO - PRÉDIO",
                        "GABINETE DO SECRETÁRIO",
                        "GUARDAS MUNICIPAIS",
                        "IMPRENSA - PRÉDIO",
                        "INDUSTRIA E COMERCIO - PRÉDIO",
                        "JUNTA DE SERVIÇO MILITAR DE UMUARAMA",
                        "LICITAÇÃO - PRÉDIO",
                        "LOCAL NÃO DEFINIDO",
                        "MAN.DIV.TECNOLOGIA INFORMAÇAO",
                        "MANUTENCAO DA LIMPEZA PUBLICA - GERAL",
                        "MANUTENCAO DA LIMPEZA PUBLICA - ROCADA",
                        "MANUTENCAO DA LIMPEZA PUBLICA DO DISTRITO VILA NOVA UNIAO",
                        "MANUTENÇÃO DA COLETA DE LIXO",
                        "MANUTENÇÃO DA LIMPEZA PUBLICA - COLETA DO LIXO VEGETAL",
                        "MANUTENÇÃO DA LIMPEZA PÚBLICA DE PRAÇAS, BOSQUES E LAGO.",
                        "MANUTENÇÃO DA LIMPEZA PÚBLICA DO DISTRITO DE LOVAT",
                        "MANUTENÇÃO DA LIMPEZA PÚBLICA DO DISTRITO DE NOVA JERUSALEM",
                        "MANUTENÇÃO DA LIMPEZA PÚBLICA DO DISTRITO DE ROBERTO SILVEIR",
                        "MANUTENÇÃO DA LIMPEZA PÚBLICA DO DISTRITO DE SANTA ELIZA",
                        "MANUTENÇÃO DA LIMPEZA PÚBLICA DO DISTRITO DE SERRA DOS DOURA",
                        "MANUTENÇÃO DA LIMPEZA PÚBLICA DO TERMINAL RODOVIÁRIO",
                        "N/A",
                        "NTI - PRÉDIO",
                        "OBRAS - PRÉDIO",
                        "ORÇAMENTO / EMPENHO - Prédio",
                        "OUVIDORIA - PRÉDIO",
                        "PATRIMÔNIO - PRÉDIO",
                        "PLANEJAMENTO ORÇAMENTÁRIO - PRÉDIO",
                        "PLANEJAMENTO URBANO - PRÉDIO",
                        "Polícia Militar Pr - BPFron 4º Companhia",
                        "POSTURA - PRÉDIO",
                        "PRESTANDO SERVIÇOS EM OUTRAS SECRETARIAS",
                        "PROCON - PROTEÇÃO E DEFESA DO CONSUMIDOR",
                        "PROCURADORIA JURÍDICA - PRÉDIO",
                        "PROGRAMA FAMÍLIA ACOLHEDORA",
                        "PROJETOS TÉCNICOS - PRÉDIO",
                        "PRONTO ATENDIMENTO",
                        "PROTOCOLO - PRÉDIO",
                        "SAP - SERVIÇO DE ATENDIMENTO PSICOLÓGICO",
                        "SEC. INOVAÇÃO, CIÊNCIA E TECNOLOGIA- PRÉDIO",
                        "SEC. MUN. DE ADMINISTRAÇÃO(C)",
                        "SEC. MUN. DE MEIO AMBIENTE",
                        "SECRETARIA DE AGRICULTURA",
                        "SECRETARIA DE SAÚDE",
                        "SECRETARIA DE SAÚDE - FROTA",
                        "SECRETARIA DE SAÚDE - M.A.C",
                        "SECRETARIA MUNICIPAL DE ADMINISTRAÇÃO",
                        "SECRETARIA MUNICIPAL DE ASSISTÊNCIA SOCIAL",
                        "SECRETARIA MUNICIPAL DE ESPORTES E LAZER",
                        "SECRETARIA MUNICIPAL DE GABINETE E GESTÃO INTEGRADA",
                        "SECRETARIA MUNICIPAL DE HABITAÇÃO E PROJETOS TÉCNICOS",
                        "SECRETARIA MUNICIPAL DE INTEGRAÇÃO COMUNITÁRIA",
                        "SECRETARIA MUNICIPAL DE OBRAS",
                        "SECRETARIA MUNICIPAL DE SAUDE",
                        "SECRETARIA MUNICIPAL DE SERVIÇOS PÚBLICOS (CC)",
                        "SECRETARIA MUNICIPAL DE SERVIÇOS RODOVIÁRIOS",
                        "SERVICOS RODOVIARIOS ESTRADAS - PATIO",
                        "SERVIÇO DE CONVIVÊNCIA DO IDOSO",
                        "SERVIÇOS GERAIS  - PRÉDIO",
                        "SERVIÇOS RODOVIÁRIOS",
                        "SESMT- CIPA",
                        "TELEFONISTAS",
                        "TERMINAL RODOVIARIO",
                        "TESOURARIA - PRÉDIO",
                        "TIRO DE GUERRA",
                    ]);
                })
                ->sortBy('local_trabalho', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->toArray();

            return $dadosOrdenados;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
