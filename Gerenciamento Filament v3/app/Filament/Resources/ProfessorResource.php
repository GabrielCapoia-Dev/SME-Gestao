<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessorResource\Pages;
use App\Models\Professor;
use App\Models\Servidor;
use App\Models\Turma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;

class ProfessorResource extends Resource
{
    protected static ?string $model = Professor::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = "Gerenciamento Escolar";
    protected static ?int $navigationSort = 1;

    public static ?string $modelLabel = 'Professor';
    public static ?string $pluralModelLabel = 'Professores';
    public static ?string $slug = 'professores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('servidor_id')
                    ->label('Servidor')
                    ->options(function () {
                        /** @var \App\Models\User|null $user */
                        $user = Auth::user();

                        // agora filtramos servidores com cargo "Professor" via lotação
                        $query = Servidor::with(['lotacao.cargo.regimeContratual'])
                            ->whereHas('lotacao.cargo', fn($q) => $q->where('nome', 'Professor'));

                        if (!$user->hasRole('Admin') && $user->servidor) {
                            $userSetorIds = $user->servidor
                                ->setores()
                                ->select('setors.id')
                                ->pluck('setors.id')
                                ->toArray();

                            if (!empty($userSetorIds)) {
                                $query->whereHas('setores', function ($q) use ($userSetorIds) {
                                    $q->whereIn('setors.id', $userSetorIds);
                                });
                            }
                        }

                        return $query->get()
                            ->mapWithKeys(function ($servidor) {
                                $cargo = $servidor->lotacao?->cargo?->nome ?? '-';
                                $regime = $servidor->lotacao?->cargo?->regimeContratual?->nome ?? '-';
                                return [
                                    $servidor->id => "[{$servidor->matricula}] {$servidor->nome} ({$cargo} - {$regime})"
                                ];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(Get $get, Set $set) => $set('turma_id', null)),

                Forms\Components\Select::make('turma_id')
                    ->label('Turma')
                    ->options(function (Get $get): array {
                        $user = Auth::user();
                        if (!$user) {
                            return [];
                        }

                        $setorIds = collect();

                        if (!empty($user->setor_id)) {
                            $setorIds->push($user->setor_id);
                        }

                        if ($user->servidor) {
                            $setorIds = $setorIds->merge(
                                $user->servidor->setores()->pluck('setors.id')
                            );

                            if ($setorIds->isEmpty() && $user->servidor->lotacao?->setor_id) {
                                $setorIds->push($user->servidor->lotacao->setor_id);
                            }
                        }

                        $setorIds = $setorIds->filter()->unique();
                        if ($setorIds->isEmpty()) {
                            return [];
                        }

                        return Turma::query()
                            ->whereIn('setor_id', $setorIds)
                            ->with(['nomeTurma:id,nome', 'siglaTurma:id,nome'])
                            ->get(['id', 'nome_turma_id', 'sigla_turma_id', 'setor_id'])
                            ->mapWithKeys(fn(Turma $turma) => [$turma->id => $turma->nomeCompleto()])
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->reactive(),

                Forms\Components\Select::make('aula_id')
                    ->label('Aula')
                    ->relationship('aula', 'nome')
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('servidor.lotacao.setor.nome')
                    ->label('Local de Trabalho')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('servidor.matricula')
                    ->label('Matricula')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('servidor.nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('turma_id')
                    ->label('Turma')
                    ->formatStateUsing(fn($state, \App\Models\Professor $record) => $record->turma?->nomeCompleto()),

                Tables\Columns\TextColumn::make('aula.nome')
                    ->label('Aula')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('servidor.lotacao.cargo.nome')
                    ->label('Cargo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('servidor.lotacao.cargo.regimeContratual.nome')
                    ->label('Regime Contratual')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('servidor.turno.nome')
                    ->label('Turno')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProfessors::route('/'),
        ];
    }
}
