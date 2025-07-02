<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\DeclaracaoDeHoraResource\Pages;
use App\Filament\Resources\DeclaracaoDeHoraResource\RelationManagers;
use App\Models\DeclaracaoDeHora;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;

class DeclaracaoDeHoraResource extends Resource
{
    protected static ?string $model = DeclaracaoDeHora::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Gerenciamento de Servidores';

    protected static ?string $modelLabel = 'Declaração de Horas';

    protected static ?string $pluralModelLabel = 'Declarações de Horas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('servidor_id')
                    ->label('Servidor')
                    ->relationship('servidor', 'nome')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        return "{$record->matricula} - {$record->nome}";
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('data')
                    ->label('Data')
                    ->required(),

                Forms\Components\TimePicker::make('hora_inicio')
                    ->seconds(false)
                    ->label('Hora de Início'),

                Forms\Components\TimePicker::make('hora_fim')
                    ->seconds(false)
                    ->label('Hora de Fim'),

                Forms\Components\Select::make('turno_id')
                    ->label('Periodo')
                    ->preload()
                    ->relationship('turno', 'nome')
                    ->required(),

                Forms\Components\TextInput::make('cid')
                    ->label('Cid'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('servidor.matricula')
                    ->label('Mat. Servidor Afastado')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.nome')
                    ->label('Nome Servidor Afastado')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.setores.nome')
                    ->label('Local de Trabalho')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.cargo.nome')
                    ->label('Cargo')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.cargo.regimeContratual.nome')
                    ->label('Regime Contratual')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('carga_horaria')
                    ->label('Carga Horaria')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('data')
                    ->label('Data')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                Tables\Columns\TextColumn::make('turno.nome')
                    ->label('Periodo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Hora de Início')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('hora_fim')
                    ->label('Hora de Fim')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cid')
                    ->label('CID')
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
                SelectFilter::make('servidor_cargo')
                    ->label('Cargo')
                    ->options(function () {
                        return \App\Models\Cargo::with('regimeContratual')->get()->mapWithKeys(function ($cargo) {
                            return [$cargo->id => "{$cargo->nome} - {$cargo->regimeContratual->nome}"];
                        })->toArray();
                    })
                    ->searchable()
                    ->multiple()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('servidor', function ($subQuery) use ($data) {
                                $subQuery->whereIn('cargo_id', $data['values']);
                            });
                        }
                    }),


                SelectFilter::make('servidor_setor')
                    ->label('Setor')
                    ->options(\App\Models\Setor::all()->pluck('nome', 'id')->toArray())
                    ->searchable()
                    ->multiple()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('servidor.setores', function ($subQuery) use ($data) {
                                $subQuery->whereIn('setores.id', $data['values']);
                            });
                        }
                    }),

                SelectFilter::make('turno_id')
                    ->label('Turno')
                    ->preload()
                    ->relationship('turno', 'nome')
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $query->where('turno_id', $data['value']);
                    }),

                // Filtro por período (data de início)
                Tables\Filters\Filter::make('updated_periodo')
                    ->label('Período de Atualização')
                    ->form([
                        Forms\Components\DatePicker::make('data_inicio')->label('De'),
                        Forms\Components\DatePicker::make('data_fim')->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['data_inicio']) && !empty($data['data_fim'])) {
                            $inicio = Carbon::parse($data['data_inicio'])->startOfDay();
                            $fim = Carbon::parse($data['data_fim'])->endOfDay();
                            $query->whereBetween('updated_at', [$inicio, $fim]);
                        } elseif (!empty($data['data_inicio'])) {
                            $inicio = Carbon::parse($data['data_inicio'])->startOfDay();
                            $query->where('updated_at', '>=', $inicio);
                        } elseif (!empty($data['data_fim'])) {
                            $fim = Carbon::parse($data['data_fim'])->endOfDay();
                            $query->where('updated_at', '<=', $fim);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->defaultFormat('pdf')
                    ->disableAdditionalColumns()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeclaracaoDeHoras::route('/'),
            'create' => Pages\CreateDeclaracaoDeHora::route('/create'),
            'edit' => Pages\EditDeclaracaoDeHora::route('/{record}/edit'),
        ];
    }
}
