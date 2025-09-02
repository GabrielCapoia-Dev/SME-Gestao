<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServidorResource\Pages;
use App\Models\Servidor;
use App\Models\Setor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Widgets\ServidoresPorCargoERegimeChart;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\MaxWidth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\ResumoGraficoServidores;
use Filament\Tables\Actions\Action;

class ServidorResource extends Resource
{
    protected static ?string $model = Servidor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static ?string $modelLabel = 'Servidor';

    protected static ?string $navigationGroup = "Gerenciamento de Servidores";

    public static ?string $pluralModelLabel = 'Servidores';

    public static ?string $slug = 'servidores';

    protected static ?int $navigationSort = -3;


    public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with(['setores', 'cargaHoraria', 'lotacao']);
    }

    public static function getWidgets(): array
    {
        return [
            ServidoresPorCargoERegimeChart::class,
            ResumoGraficoServidores::class,
        ];
    }

    public static function form(Form $form): Form
    {

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $naoAdminOuRH = fn(): bool => ! $user?->hasAnyRole(['Admin', 'RH']);
        $adminOuRH = fn(): bool => $user?->hasAnyRole(['Admin', 'RH']);


        return $form
            ->schema([
                Forms\Components\TextInput::make('matricula')
                    ->unique(ignoreRecord: true)
                    ->label('Matricula')
                    ->numeric()
                    ->required()
                    ->disabled($naoAdminOuRH)
                    ->dehydrated($adminOuRH),

                Forms\Components\TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->disabled($naoAdminOuRH)
                    ->dehydrated($adminOuRH),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->unique(ignoreRecord: true)
                    ->email()
                    ->required(),

                Forms\Components\Select::make('cargo_id')
                    ->label('Cargo')
                    ->options(function () {
                        return \App\Models\Cargo::with('regimeContratual')->get()->mapWithKeys(function ($cargo) {
                            return [
                                $cargo->id => "{$cargo->nome} - {$cargo->regimeContratual?->nome}"
                            ];
                        })->toArray();
                    })
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\Select::make('turno_id')
                    ->label('Turno')
                    ->preload()
                    ->relationship('turno', 'nome')
                    ->required()
                    ->disabled($naoAdminOuRH)
                    ->dehydrated($adminOuRH),

                Forms\Components\Select::make('setores')
                    ->label('Local de Trabalho')
                    ->relationship('setores', 'nome')
                    ->preload()
                    ->multiple()
                    ->required()
                    ->disabled($naoAdminOuRH)
                    ->dehydrated($adminOuRH),

                Forms\Components\Select::make('lotacao_id')
                    ->label('Lotação')
                    ->relationship('lotacao', 'nome')
                    ->preload()
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\Lotacao::query()
                            ->where('nome', 'like', "%{$search}%")
                            ->orWhere('codigo', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($lotacao) {
                                return [
                                    $lotacao->id => "{$lotacao->codigo} - {$lotacao->cargo?->nome} {$lotacao->cargo?->regimeContratual?->nome} | {$lotacao->setor?->nome}"
                                ];
                            });
                    })
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        return "{$record?->codigo} - {$record->cargo?->nome} {$record->cargo?->regimeContratual?->nome} | {$record->setor?->nome}";
                    })
                    ->required()
                    ->disabled($naoAdminOuRH)
                    ->dehydrated($adminOuRH),

                Forms\Components\DatePicker::make('data_admissao')
                    ->label('Data de Admissão')
                    ->required()
                    ->disabled($naoAdminOuRH)
                    ->dehydrated($adminOuRH),

                // ✅ PERMITIDO PARA TODOS
                Forms\Components\Section::make('Carga Horária')
                    ->schema([
                        Forms\Components\Group::make()
                            ->relationship('cargaHoraria')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TimePicker::make('entrada')
                                            ->label('Entrada')
                                            ->required()
                                            ->seconds(false),

                                        Forms\Components\TimePicker::make('saida_intervalo')
                                            ->label('Saída para Almoço (Apenas servidores 40 horas)')
                                            ->seconds(false),

                                        Forms\Components\TimePicker::make('entrada_intervalo')
                                            ->label('Retorno do Almoço (Apenas servidores 40 horas)')
                                            ->seconds(false),

                                        Forms\Components\TimePicker::make('saida')
                                            ->label('Saída')
                                            ->required()
                                            ->seconds(false),
                                    ]),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsed(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->filtersFormWidth(MaxWidth::Full)
            ->columns([
                Tables\Columns\TextColumn::make('setores_list')
                    ->label('Locais de Trabalho')
                    ->getStateUsing(
                        fn($record) =>
                        $record->setores->pluck('nome')->join(', ')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas(
                            'setores',
                            fn($q) =>
                            $q->where('nome', 'like', "%{$search}%")
                        );
                    })
                    ->sortable(query: function (Builder $query, string $direction) {
                        $query->orderBy(
                            Setor::select('nome')
                                ->join('servidor_setor', 'setors.id', '=', 'servidor_setor.setor_id')
                                ->whereColumn('servidor_setor.servidor_id', 'servidores.id')
                                ->limit(1),
                            $direction
                        );
                    }),


                Tables\Columns\TextColumn::make('lotacao.codigo')
                    ->label('Codigo da Lotação')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lotacao.nome')
                    ->label('Nome da Lotação')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn($record) => $record->lotacao?->nome ?? '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lotacao.setor.nome')
                    ->label('Local de Lotação')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn($record) => $record->lotacao?->setor?->nome ?? '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('matricula')
                    ->sortable()
                    ->label('Matricula')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nome')
                    ->sortable()
                    ->label('Nome')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('lotacao.cargo.nome')
                    ->sortable()
                    ->label('Cargo')
                    ->getStateUsing(fn($record) => $record->lotacao?->cargo?->nome ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('lotacao.cargo.regimeContratual.nome')
                    ->sortable()
                    ->label('Regime Contratual')
                    ->getStateUsing(fn($record) => $record->lotacao?->cargo?->regimeContratual?->nome ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('turno.nome')
                    ->sortable()
                    ->label('Turno')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('cargaHoraria')
                    ->label('Carga Horária')
                    ->getStateUsing(function ($record) {
                        $carga = $record->cargaHoraria;

                        if (!$carga) return '-';

                        return collect([
                            $carga->entrada ? 'Entrada: ' . $carga->entrada : null,
                            $carga->saida_intervalo ? 'Saída Almoço: ' . $carga->saida_intervalo : null,
                            $carga->entrada_intervalo ? 'Retorno: ' . $carga->entrada_intervalo : null,
                            $carga->saida ? 'Saída: ' . $carga->saida : null,
                        ])
                            ->filter()
                            ->implode(' | ');
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data_admissao')
                    ->label('Data de Admissão')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d/m/Y'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

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
                SelectFilter::make('lotacao.cargo_id')
                    ->label('Cargo')
                    ->multiple()
                    ->preload()
                    ->options(function () {
                        return \App\Models\Cargo::with('regimeContratual')->get()->mapWithKeys(function ($cargo) {
                            return [
                                $cargo->id => "{$cargo->nome} - {$cargo->regimeContratual?->nome}"
                            ];
                        })->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['values'])) return;
                        $query->whereHas('lotacao', function ($q) use ($data) {
                            $q->whereIn('cargo_id', $data['values']);
                        });
                    }),

                SelectFilter::make('setores')
                    ->label('Local de Trabalho')
                    ->preload()
                    ->relationship('setores', 'nome')
                    ->multiple(),

                SelectFilter::make('turno_id')
                    ->label('Turno')
                    ->preload()
                    ->relationship('turno', 'nome')
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $query->where('turno_id', $data['value']);
                    }),

                SelectFilter::make('lotacao_id')
                    ->label('Lotação')
                    ->multiple()
                    ->preload()
                    ->options(function () {
                        return \App\Models\Lotacao::with('setor')->get()->mapWithKeys(function ($lotacao) {
                            $setorNome = $lotacao->setor?->nome ?? '-';
                            return [
                                $lotacao->id => "{$lotacao->codigo} - {$lotacao->nome} ({$setorNome})"
                            ];
                        })->toArray();
                    }),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->defaultFormat('pdf')
                    ->label('Exportar')
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
            'index' => Pages\ListServidors::route('/'),
            'create' => Pages\CreateServidor::route('/create'),
            'edit' => Pages\EditServidor::route('/{record}/edit'),
        ];
    }
}
