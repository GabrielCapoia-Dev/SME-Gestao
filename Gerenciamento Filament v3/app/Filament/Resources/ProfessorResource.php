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
                        return Servidor::with(['cargo.regimeContratual'])
                            ->whereHas('cargo', fn($q) => $q->where('nome', 'Professor'))
                            ->get()
                            ->mapWithKeys(function ($servidor) {
                                $cargo = $servidor->cargo?->nome ?? '-';
                                $regime = $servidor->cargo?->regimeContratual?->nome ?? '-';
                                $label = "{$servidor->matricula} - {$servidor->nome} ({$cargo} - {$regime})";
                                return [$servidor->id => $label];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(Get $get, Set $set) => $set('turma_id', null)),

                Forms\Components\Select::make('turma_id')
                    ->label('Turma')
                    ->options(function (Get $get) {
                        $servidorId = $get('servidor_id');

                        if (!$servidorId) {
                            return [];
                        }

                        $servidor = \App\Models\Servidor::with('lotacao.setor')->find($servidorId);
                        $setorId = $servidor?->lotacao?->setor?->id;

                        if (!$setorId) {
                            return [];
                        }

                        return \App\Models\Turma::where('setor_id', $setorId)
                            ->with(['nomeTurma', 'siglaTurma'])
                            ->get()
                            ->mapWithKeys(fn($turma) => [$turma->id => $turma->nomeCompleto()]);
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
                    ->formatStateUsing(function ($state, \App\Models\Professor $record) {
                        return $record->turma?->nomeCompleto();
                    }),
                Tables\Columns\TextColumn::make('aula.nome')
                    ->label('Aula')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('servidor.cargo.nome')
                    ->label('Cargo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('servidor.cargo.regimeContratual.nome')
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
            ->filters([
                //
            ])
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
