<?php

namespace Database\Seeders;

use App\Models\CargaHoraria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Turno;
use App\Models\RegimeContratual;
use App\Models\Cargo;
use App\Models\Lotacao;
use App\Models\NomeTurma;
use App\Models\Servidor;
use App\Models\Setor;
use App\Models\SiglaTurma;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\TipoAtestado;
use App\Services\ApiFilterService;
use App\Services\GoogleSheetService;

class DatabaseSeeder extends Seeder
{
    private function loading($label, $current, $total)
    {
        $this->command->getOutput()->write("\r⏳ {$label} ######################## ({$current}/{$total})");
        usleep(120000); // 0.12s só pra ver o efeito
    }

    private function done($label, $total)
    {
        $this->command->getOutput()->writeln("\r✅ {$label} concluído! ({$total})           ");
    }


    public function run(): void
    {
        $this->command->info('Iniciando o seeding completo do sistema...');
        // Limpa cache das permissões do Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permissões
        $permissionsList = [
            'Listar Usuários',
            'Criar Usuários',
            'Editar Usuários',
            'Excluir Usuários',
            'Listar Níveis de Acesso',
            'Criar Níveis de Acesso',
            'Editar Níveis de Acesso',
            'Excluir Níveis de Acesso',
            'Listar Permissões de Execução',
            'Criar Permissões de Execução',
            'Editar Permissões de Execução',
            'Excluir Permissões de Execução',
            'Listar Dominios de Email',
            'Criar Dominios de Email',
            'Editar Dominios de Email',
            'Excluir Dominios de Email',
            'Listar Turnos',
            'Criar Turnos',
            'Editar Turnos',
            'Excluir Turnos',
            'Listar Turmas',
            'Criar Turmas',
            'Editar Turmas',
            'Excluir Turmas',
            'Listar Tipos de Atestados',
            'Criar Tipos de Atestados',
            'Editar Tipos de Atestados',
            'Excluir Tipos de Atestados',
            'Listar Setores',
            'Criar Setores',
            'Editar Setores',
            'Excluir Setores',
            'Listar Servidores',
            'Criar Servidores',
            'Editar Servidores',
            'Excluir Servidores',
            'Listar Regimes Contratuais',
            'Criar Regimes Contratuais',
            'Editar Regimes Contratuais',
            'Excluir Regimes Contratuais',
            'Listar Professores',
            'Criar Professores',
            'Editar Professores',
            'Excluir Professores',
            'Listar Lotações',
            'Criar Lotações',
            'Editar Lotações',
            'Excluir Lotações',
            'Listar Declarações de Hora',
            'Criar Declarações de Hora',
            'Editar Declarações de Hora',
            'Excluir Declarações de Hora',
            'Listar Cargos',
            'Criar Cargos',
            'Editar Cargos',
            'Excluir Cargos',
            'Listar Aulas',
            'Criar Aulas',
            'Editar Aulas',
            'Excluir Aulas',
            'Listar Afastamentos',
            'Criar Afastamentos',
            'Editar Afastamentos',
            'Excluir Afastamentos'
        ];

        $rhPermissionsList = [
            'Listar Cargos',
            'Criar Cargos',
            'Editar Cargos',
            'Excluir Cargos',
            'Listar Lotações',
            'Criar Lotações',
            'Editar Lotações',
            'Excluir Lotações',
            'Listar Regimes Contratuais',
            'Criar Regimes Contratuais',
            'Editar Regimes Contratuais',
            'Excluir Regimes Contratuais',
            'Listar Setores',
            'Criar Setores',
            'Editar Setores',
            'Excluir Setores',
            'Listar Tipos de Atestados',
            'Criar Tipos de Atestados',
            'Editar Tipos de Atestados',
            'Excluir Tipos de Atestados',
            'Listar Turnos',
            'Criar Turnos',
            'Editar Turnos',
            'Excluir Turnos',
        ];

        $UEPermissionsList = [
            'Listar Afastamentos',
            'Criar Afastamentos',
            'Editar Afastamentos',
            'Excluir Afastamentos',
            'Listar Declarações de Hora',
            'Criar Declarações de Hora',
            'Editar Declarações de Hora',
            'Excluir Declarações de Hora',
            'Listar Servidores',
            'Criar Servidores',
            'Editar Servidores',
            'Listar Professores',
            'Criar Professores',
            'Editar Professores',
            'Listar Turmas',
            'Criar Turmas',
            'Editar Turmas',
            'Excluir Turmas',
        ];

        // Criação de permissões
        foreach ($permissionsList as $i => $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
            $this->loading('Criando permissões', $i + 1, count($permissionsList));
        }
        $this->done('Permissões', count($permissionsList));

        // Criação de roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Admin']);
        $rhRole = Role::firstOrCreate(['name' => 'RH']);
        $UERole = Role::firstOrCreate(['name' => 'Unidade Educacional']);

        $superAdminRole->syncPermissions($permissionsList);
        $rhRole->syncPermissions($rhPermissionsList);
        $UERole->syncPermissions($UEPermissionsList);

        $this->done('Roles criadas', 3);

        // Criação do usuário admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'email_approved' => true
            ]
        );

        $this->command?->info('Admin criado com sucesso!');


        $adminUser->assignRole($superAdminRole);

        $this->command?->info('Passando a regra de admin para o usuário admin');


        // ========================
        // Criação de Turnos
        // ========================
        $turnos = [
            'Manhã',
            'Tarde',
            'Noite',
            'Integral',
        ];

        foreach ($turnos as $i => $turno) {
            Turno::firstOrCreate(['nome' => $turno]);
            $this->loading('Criando turnos', $i + 1, count($turnos));
        }
        $this->done('Turnos', count($turnos));

        // -------------------------------------------------------
        // Regimes
        // -------------------------------------------------------
        $regimes = [
            'Estatutário',
            'C.L.T',
            'PSS',
            'Comissionado',
        ];

        $regimeContratualIds = [];

        foreach ($regimes as $i => $regime) {
            $regimeModel = RegimeContratual::firstOrCreate(['nome' => $regime]);
            $regimeContratualIds[$regime] = $regimeModel->id;
            $this->loading('Criando regimes', $i + 1, count($regimes));
        }
        $this->done('Regimes contratuais', count($regimes));


        // -------------------------------------------------------
        // Cargos (com restrições por regime)
        // -------------------------------------------------------
        $cargosPorRegime = [
            'Professor'             => ['Estatutário', 'PSS', 'C.L.T', 'Comissionado'],
            'Auxiliar de Serviços Gerais e Secretário Escolar' => ['Estatutário', 'PSS', 'C.L.T'],
            'Acessor Especial'      => ['Comissionado'],
        ];

        $totalCargos = collect($cargosPorRegime)->map(fn($r) => count($r))->sum();
        $count = 0;

        foreach ($cargosPorRegime as $cargoNome => $regimesValidos) {
            foreach ($regimesValidos as $regimeNome) {
                $regimeId = $regimeContratualIds[$regimeNome];
                Cargo::firstOrCreate(
                    ['nome' => $cargoNome, 'regime_contratual_id' => $regimeId],
                    ['descricao' => "{$cargoNome} - {$regimeNome}"]
                );
                $this->loading('Criando cargos', ++$count, $totalCargos);
            }
        }
        $this->done('Cargos', $totalCargos);



        // ========================
        // Criação de Tipos de Atestados
        // ========================

        $tipos = [
            'Licença Tratamento de Saúde',
            'Férias',
            'Licença Premio',
            'Licença Gala',
            'Licença Luto',
            'Licença Sem Vencimento',
            'Licença Maternidade',
            'Licença para assumir outro cargo',
            'Licença Acidente de Trabalho',
            'Licença Serviço Militar',
            'Licenças Outras',
            'Licença para Acompanhamento',
        ];

        foreach ($tipos as $nome) {
            TipoAtestado::firstOrCreate(['nome' => $nome]);
            $this->loading('Criando tipos de atestados', $i + 1, count($tipos));
        }
        $this->done('Tipos de atestados', count($tipos));

        // ========================
        // Criação de Tipos de NomeTurmas
        // ========================

        $tipos = [
            '1º Ano',
            '2º Ano',
            '3º Ano',
            '4º Ano',
            '5º Ano',
            'Infantil 4',
            'Infantil 5',
            'Maternal I',
            'Maternal II',
            'Maternal III',
            'Berçario I',
            'Berçario II',
            'Berçario III',
        ];

        foreach ($tipos as $nome) {
            NomeTurma::firstOrCreate(['nome' => $nome]);
            $this->loading('Criando turmas', $i + 1, count($tipos));
        }
        $this->done('Turmas', count($tipos));

        // -------------------------------------------------------
        // Sigla Turmas
        // -------------------------------------------------------
        $siglas = range('A', 'Z');
        foreach ($siglas as $i => $nome) {
            SiglaTurma::firstOrCreate(['nome' => $nome]);
            $this->loading('Criando siglas de turmas', $i + 1, count($siglas));
        }
        $this->done('Siglas de turmas', count($siglas));


        // {



        // -------------------------------------------------------
        // Setores via API
        // -------------------------------------------------------
        $service = new ApiFilterService();
        $setoresApi  = $service->obterLocalTrabalho();
        $setores = [];
        foreach ($setoresApi as $i => $setorData) {
            $setores[] = Setor::firstOrCreate(['nome' => $setorData['local_trabalho']]);
            $this->loading('Criando setores', $i + 1, count($setoresApi));
        }
        $this->done('Setores', count($setoresApi));



        // -------------------------------------------------------
        // Importar lotações a partir da planilha Google Sheets
        // -------------------------------------------------------
        $spreadsheetId = '1bawy7mtk34OVPans34FcJKa8HdH2wHxjW3YGlHPSPOk';
        $googleSheet = new GoogleSheetService();

        try {
            // 1) Importa as lotações com código, descrição e setor_id
            $resultado = $googleSheet->importarLotacoes($spreadsheetId, 'dados!A:D');
            $this->command->info($resultado['message'] ?? '✅ Lotações importadas com sucesso!');

            // 2) Vincula cargos às lotações com base na planilha
            $resultadoVinculo = $googleSheet->vincularCargosEmLotacoes($spreadsheetId, 'dados!A:D');
            $this->command->info($resultadoVinculo['message'] ?? '✅ Cargos vinculados com sucesso!');

            // Caso existam registros não encontrados, mostra aviso
            if (!empty($resultadoVinculo['nao_encontrados'])) {
                foreach ($resultadoVinculo['nao_encontrados'] as $erro) {
                    $this->command->warn("⚠️ {$erro}");
                }
            }
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao importar/vincular lotações: ' . $e->getMessage());
        }



        // // ========================
        // // Criação de Lotações
        // // ========================
        // $lotacoes = [];
        // $cargosAll = Cargo::all();
        // $totalLotacoes = count($setores) * $cargosAll->count();
        // $count = 0;
        // foreach ($setores as $setor) {
        //     foreach ($cargosAll as $cargo) {
        //         $lotacoes[] = Lotacao::create([
        //             'nome' => "{$setor->nome} - {$cargo->nome} - {$cargo->regimeContratual->nome}",
        //             'codigo' => fake()->unique()->numerify('013.123.###.###'),
        //             'descricao' => "Lotação para {$cargo->nome} - {$cargo->regimeContratual->nome} em {$setor->nome}",
        //             'setor_id' => $setor->id,
        //             'cargo_id' => $cargo->id,
        //         ]);
        //         $this->loading('Criando lotações', ++$count, $totalLotacoes);
        //     }
        // }
        // $this->done('Lotações', $totalLotacoes);


        // ========================
        // Criação de X servidores aleatórios com setor e carga horária
        // ========================

        // Pergunta no terminal
        $numeroServidores = (int) $this->command->ask(
            'Quantos servidores deseja criar?',
            500 // valor padrão se só apertar Enter
        );

        $lotacoes = \App\Models\Lotacao::all();


        for ($i = 1; $i <= $numeroServidores; $i++) {
            $servidor = Servidor::create([
                'nome' => fake()->name(),
                'matricula' => str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => fake()->unique()->safeEmail(),
                'cargo_id' => Cargo::inRandomOrder()->first()->id,
                'turno_id' => Turno::inRandomOrder()->first()->id,
                'lotacao_id' => collect($lotacoes)->random()->id,
                'data_admissao' => fake()->date('Y-m-d', '-10 years'),
            ]);

            // Vincular setor aleatório
            $servidor->setores()->sync([collect($setores)->random()->id]);

            // Definir carga horária conforme turno
            $turnoNome = $servidor->turno->nome;

            switch ($turnoNome) {
                case 'Integral':
                    $entrada = '08:00';
                    $saida_intervalo = '12:00';
                    $entrada_intervalo = '13:30';
                    $saida = '17:00';
                    break;

                case 'Manhã':
                    $entrada = '08:00';
                    $saida_intervalo = '12:00';
                    $entrada_intervalo = null;
                    $saida = null;
                    break;

                case 'Tarde':
                    $entrada = null;
                    $saida_intervalo = null;
                    $entrada_intervalo = '13:30';
                    $saida = '17:00';
                    break;

                case 'Noite':
                    $entrada = '18:00';
                    $saida_intervalo = null;
                    $entrada_intervalo = null;
                    $saida = '23:00';
                    break;

                default:
                    $entrada = $saida_intervalo = $entrada_intervalo = $saida = null;
                    break;
            }

            // Criar carga horária junto com o servidor
            CargaHoraria::create([
                'servidor_id' => $servidor->id,
                'entrada' => $entrada,
                'saida_intervalo' => $saida_intervalo,
                'entrada_intervalo' => $entrada_intervalo,
                'saida' => $saida,
            ]);

            $this->loading('Criando servidores e cargas horárias', $i, $numeroServidores);
        }

        $this->done('Servidores e cargas horárias', $numeroServidores);

        $this->command->info('✅ Seed completo finalizado com sucesso!');

        $rhUser = User::firstOrCreate(
            ['email' => 'rh@rh.com'],
            [
                'name' => 'RH',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'email_approved' => true,
                'setor_id' => Setor::inRandomOrder()->first()->id
            ]
        );

        $UEUser = User::firstOrCreate(
            ['email' => 'unidadeEducacional@unidadeEducacional.com'],
            [
                'name' => 'Unidade Educacional',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'email_approved' => true,
                'setor_id' => Setor::inRandomOrder()->first()->id

            ]
        );


        $rhUser->assignRole($rhRole);
        $UEUser->assignRole($UERole);

        $this->command->info('✅ Usuários RH e UE criados com sucesso!');

        // }

        // 👉 Chamar outros seeders
        $this->call([
            AtestadoSeeder::class,
            AulaSeeder::class,
            DeclaracaoDeHoraSeeder::class,
            TurmaSeeder::class,
            ProfessorSeeder::class,
        ]);
    }
}
