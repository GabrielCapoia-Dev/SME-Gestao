<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Models\Servidor;
// use App\Services\ServidorApiService;
// use Illuminate\Support\Arr;

// class SincronizarServidores extends Command
// {
//     protected $signature = 'servidores:sincronizar';
//     protected $description = 'Sincroniza servidores da API para o banco local';

//     public function handle(ServidorApiService $service)
//     {
//         $this->info('Iniciando sincronização...');

//         $totalVerificados = 0;
//         $novos = 0;
//         $atualizados = 0;
//         $semAlteracoes = 0;

//         for ($pagina = 0; $pagina < 3; $pagina++) {
//             $this->line("🔄 Processando página $pagina...");
//             $dados = $service->obterServidores(1, 2025, $pagina);

//             foreach ($dados['content'] ?? [] as $item) {
//                 $localTrabalho = strtoupper($item['localTrabalho'] ?? '');
//                 $descricaoCargo = strtoupper($item['descricaoCargo'] ?? '');
//                 $situacaoBruta = strtoupper($item['situacao'] ?? '');

//                 $incluir = (
//                     (
//                         str_contains($localTrabalho, 'ESCOLA') ||
//                         str_contains($localTrabalho, 'ESC') ||
//                         str_contains($localTrabalho, 'ESC.') ||
//                         str_contains($localTrabalho, 'C.M.E.I') ||
//                         str_contains($localTrabalho, 'C.M.E.I.') ||
//                         str_contains($localTrabalho, 'CMEI') ||
//                         str_contains($localTrabalho, 'ADMIN. DIRETORIA DE EDUCAÇAO') ||
//                         str_contains($localTrabalho, 'BIBLIOTECA ITINERANTE') ||
//                         str_contains($localTrabalho, 'MERENDA ESCOLAR') ||
//                         str_contains($localTrabalho, 'COORDENAÇÃO DA EDUCAÇÃO - SECRETARIA') ||
//                         str_contains($localTrabalho, 'CENTRO DE ATENDIMENTO EDUCACIONAL INTEGRADO - CAEI') ||
//                         str_contains($localTrabalho, 'CENTRO MUNICIPAL DE DESENVOLVIMENTO E PESQUISA EM EDUCAÇÃO') ||
//                         str_contains($localTrabalho, 'MOTORISTAS EDUCAÇÃO - SECRETARIA') ||
//                         str_contains($localTrabalho, 'SISPUMU - SINDICATO DOS SERVIDORES PÚBLICOS DE UMUARAMA') ||
//                         str_contains($localTrabalho, 'UAB') ||
//                         str_contains($descricaoCargo, 'PROF') ||
//                         str_contains($descricaoCargo, 'PROF.') ||
//                         str_contains($descricaoCargo, 'PROFESSOR')
//                     )
//                     &&
//                     (
//                         in_array($situacaoBruta, ['ATIVO', 'INATIVO', 'EXONERADO']) ||
//                         str_contains($situacaoBruta, 'AFASTADO')
//                     )
//                     &&
//                     !str_contains($localTrabalho, 'FUNDAÇÃO CULTURAL DE UMUARAMA') &&
//                     !str_contains($localTrabalho, 'CENTRO DA JUVENTUDE') &&
//                     !str_contains($localTrabalho, 'SECRETARIA MUNICIPAL DE ESPORTES E LAZER') &&
//                     !str_contains($localTrabalho, 'SECRETARIA MUNICIPAL DE INTEGRAÇÃO COMUNITÁRIA') &&
//                     !str_contains($localTrabalho, 'SECRETARIA MUNICIPAL DE HABITAÇÃO E PROJETOS TÉCNICOS') &&
//                     !str_contains($localTrabalho, 'SECRETARIA MUNICIPAL DE GABINETE E GESTÃO INTEGRADA') &&
//                     !str_contains($localTrabalho, 'SECRETARIA MUNICIPAL DE SAUDE') &&
//                     !str_contains($localTrabalho, 'LOCAL NÃO DEFINIDO')
//                 );

//                 if (!$incluir) {
//                     continue;
//                 }

//                 $totalVerificados++;

//                 $status = '';
//                 $situacao = $situacaoBruta;
//                 if (str_contains($item['situacao'], '-')) {
//                     [$status, $situacao] = array_map('trim', explode('-', $item['situacao'], 2));
//                 }

//                 $payload = [
//                     'entidade' => $item['entidade'],
//                     'nome' => $item['nome'],
//                     'descricaoCargo' => $descricaoCargo,
//                     'descricaoLotacao' => $item['descricaoLotacao'] ?? null,
//                     'descricaoClasse' => $item['descricaoClasse'] ?? null,
//                     'descricaoNatureza' => $item['descricaoNatureza'] ?? null,
//                     'faixa' => $item['faixa'] ?? null,
//                     'status' => $status,
//                     'situacao' => $situacao,
//                     'dataAdmissao' => $item['dataAdmissao'] ?? null,
//                     'dataDemissao' => $item['dataDemissao'] ?? null,
//                     'localTrabalho' => $localTrabalho,
//                     'horarioEntrada' => isset($item['horarioEntrada']) ? date('H:i:s', strtotime($item['horarioEntrada'])) : null,
//                     'horarioSaidaIntervalo' => isset($item['horarioSaidaIntervalo']) ? date('H:i:s', strtotime($item['horarioSaidaIntervalo'])) : null,
//                     'horarioEntradaIntervalo' => isset($item['horarioEntradaIntervalo']) ? date('H:i:s', strtotime($item['horarioEntradaIntervalo'])) : null,
//                     'horarioSaida' => isset($item['horarioSaida']) ? date('H:i:s', strtotime($item['horarioSaida'])) : null,
//                     'horarioEspecial' => $item['horarioEspecial'] ?? null,
//                     'horarioTrabalho' => $item['horarioTrabalho'] ?? null,
//                     'horarioSaidaFormated' => $item['horarioSaidaFormated'] ?? null,
//                     'horarioEntradaFormated' => $item['horarioEntradaFormated'] ?? null,
//                     'horarioSaidaIntervaloFormated' => $item['horarioSaidaIntervaloFormated'] ?? null,
//                     'horarioEntradaIntervaloFormated' => $item['horarioEntradaIntervaloFormated'] ?? null,
//                 ];

//                 $servidor = Servidor::where('matricula', $item['matricula'])->first();

//                 if (!$servidor) {
//                     Servidor::create(array_merge(['matricula' => $item['matricula']], $payload));
//                     $novos++;
//                     $this->info("➕ Novo servidor cadastrado: {$item['nome']}");
//                 } else {
//                     $diff = array_diff_assoc($payload, Arr::only($servidor->toArray(), array_keys($payload)));

//                     if (!empty($diff)) {
//                         $servidor->update($payload);
//                         $atualizados++;
//                         $this->info("✏️  Servidor atualizado: {$item['nome']}");
//                     } else {
//                         $semAlteracoes++;
//                         $this->line("✔️  Sem alterações: {$item['nome']}");
//                     }
//                 }
//             }
//         }

//         $this->info('✅ Sincronização finalizada.');
//         $this->line("📊 Total verificados: $totalVerificados");
//         $this->line("➕ Novos: $novos");
//         $this->line("✏️  Atualizados: $atualizados");
//         $this->line("✔️  Sem alterações: $semAlteracoes");
//     }
// }
