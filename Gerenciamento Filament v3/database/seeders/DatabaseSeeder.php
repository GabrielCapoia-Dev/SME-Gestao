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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
        ];

        // Criação de permissões
        foreach ($permissionsList as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Criação de roles
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Usuário']);
        Role::firstOrCreate(['name' => 'Secretário']);
        Role::firstOrCreate(['name' => 'Coordenador']);
        Role::firstOrCreate(['name' => 'Diretor']);

        $superAdminRole->syncPermissions($permissionsList);
        $adminRole->syncPermissions($permissionsList);

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

        $adminUser->assignRole($adminRole);

        // ========================
        // Criação de Turnos
        // ========================
        $turnos = [
            'Manhã',
            'Tarde',
            'Noite',
            'Integral',
        ];

        foreach ($turnos as $turno) {
            Turno::firstOrCreate(['nome' => $turno]);
        }

        // ========================
        // Criação de Regime Contratual
        // ========================
        $regimes = [
            'Estatutário',
            'C.L.T',
            'PSS',
        ];

        $regimeContratualIds = [];

        foreach ($regimes as $regime) {
            $regimeModel = RegimeContratual::firstOrCreate(['nome' => $regime]);
            $regimeContratualIds[$regime] = $regimeModel->id;
        }

        // ========================
        // Criação de Cargos
        // ========================
        $cargos = [
            'Professor',
            'Auxiliar Serviços Gerais',
            'Secretário Escolar',
        ];

        foreach ($cargos as $cargoNome) {
            foreach ($regimeContratualIds as $regimeNome => $regimeId) {
                Cargo::firstOrCreate(
                    [
                        'nome' => $cargoNome,
                        'regime_contratual_id' => $regimeId,
                    ],
                    [
                        'descricao' => "{$cargoNome} - {$regimeNome}",
                    ]
                );
            }
        }


        // ========================
        // Criação de Setores (escolas)
        // ========================
        $nomesEscolas = [
            'Escola Municipal São José',
            'Colégio Estadual Machado de Assis',
            'CMEI Pingo de Gente',
            'Escola Municipal Cecília Meireles',
            'CMEI Gira Mundo',
            'Colégio Estadual Santos Dumont',
            'Escola Municipal Heitor Villa-Lobos',
            'CMEI Jardim da Alegria',
            'Escola Municipal Juscelino Kubitschek',
        ];

        $setores = [];

        foreach ($nomesEscolas as $nome) {
            $setores[] = Setor::firstOrCreate(
                ['nome' => $nome],
                [
                    'email' => fake()->unique()->safeEmail(),
                    'telefone' => fake()->phoneNumber(),
                ]
            );
        }

        // ========================
        // Criação de Lotações
        // ========================
        $lotacoes = [];

        foreach ($setores as $setor) {
            foreach (Cargo::all() as $cargo) {
                $lotacoes[] = Lotacao::create([
                    'nome' => "{$cargo->nome} - {$setor->nome}",
                    'codigo' => fake()->unique()->numerify('013.123.###'),
                    'descricao' => "Lotação para {$cargo->descricao} em {$setor->nome}",
                    'setor_id' => $setor->id,
                    'cargo_id' => $cargo->id,
                ]);
            }
        }

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
            TipoAtestado::create([
                'nome' => $nome,
            ]);
        }

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
        ];

        foreach ($tipos as $nome) {
            NomeTurma::create([
                'nome' => $nome,
            ]);
        }

        // ========================
        // Criação de Tipos de SiglaTurmas
        // ========================

        $tipos = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
        ];

        foreach ($tipos as $nome) {
            SiglaTurma::create([
                'nome' => $nome,
            ]);
        }

        // ========================
        // Criação de X servidores aleatórios com setor
        // ========================
        $numeroServidores = 100;

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

            // Vincular o servidor a 1 a 3 setores aleatórios
            $setoresAleatorios = collect($setores)->random(rand(1, 3))->pluck('id');
            $servidor->setores()->attach($setoresAleatorios);

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


            CargaHoraria::create([
                'servidor_id' => $servidor->id,
                'entrada' => $entrada,
                'saida_intervalo' => $saida_intervalo,
                'entrada_intervalo' => $entrada_intervalo,
                'saida' => $saida,
            ]);
        }
    }
}
