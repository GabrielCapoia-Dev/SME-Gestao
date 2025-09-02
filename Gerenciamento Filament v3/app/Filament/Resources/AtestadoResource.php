<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtestadoResource\Pages;
use App\Models\Atestado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Servidor;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\AtestadoResource\Widgets\ServidorAtestadoChart;
use App\Filament\Resources\AtestadoResource\Widgets\AtestadosPorTiposChart;
use App\Filament\Widgets\ResumoGraficoServidores;

class AtestadoResource extends Resource
{
    protected static ?string $model = Atestado::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-minus';

    protected static ?string $navigationGroup = 'Gerenciamento de Servidores';

    protected static ?string $modelLabel = 'Afastamento';

    protected static ?string $pluralModelLabel = 'Afastamentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('servidor_id')
                    ->label('Servidor')
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        /** @var \App\Models\User|null $user */
                        $user = Auth::user();

                        $servidoresQuery = Servidor::query();

                        if ($user->servidor) {
                            $userSetorIds = $user->servidor->setores()->pluck('setors.id')->toArray();

                            if (!empty($userSetorIds)) {
                                $servidoresQuery->whereHas('setores', function ($query) use ($userSetorIds) {
                                    $query->whereIn('setors.id', $userSetorIds);
                                })->pluck('nome', 'id');

                                return $servidoresQuery
                                    ->get()
                                    ->mapWithKeys(function ($servidor) {
                                        return [
                                            $servidor->id => "[{$servidor->matricula}] {$servidor->nome}"
                                        ];
                                    })
                                    ->toArray();
                            }
                        }

                        return $servidoresQuery
                            ->get()
                            ->mapWithKeys(function ($servidor) {
                                return [
                                    $servidor->id => "[{$servidor->matricula}] {$servidor->nome}"
                                ];
                            })
                            ->toArray();
                    })
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->matricula} - {$record->nome}")
                    ->required(),

                Forms\Components\Select::make('tipo_atestado_id')
                    ->label('Tipo de Atestado')
                    ->relationship('tipoAtestado', 'nome')
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('data_inicio')
                    ->label('Data de Início')
                    ->required(),

                Forms\Components\DatePicker::make('data_fim')
                    ->label('Data de Fim')
                    ->required(fn(callable $get) => !$get('prazo_indeterminado'))
                    ->disabled(fn(callable $get) => $get('prazo_indeterminado')),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('cid')
                            ->label('CID')
                            ->maxLength(255),

                        Forms\Components\Toggle::make('prazo_indeterminado')
                            ->label('Prazo Indeterminado?')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-s-check')
                            ->offIcon('heroicon-s-x-mark')
                            ->required()
                            ->default(false)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $set('data_fim', null);
                                }
                            }),
                    ]),

                Forms\Components\Section::make('Substituto (Opcional)')
                    ->description('Informe o servidor que está substituindo o servidor afastado, se houver.')
                    ->schema([
                        Forms\Components\Select::make('substituto_id')
                            ->label('Servidor Substituto')
                            ->relationship('substituto', 'nome')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return "{$record->matricula} - {$record->nome}";
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Sem substituto')
                            ->columnSpanFull()
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->filtersFormWidth(MaxWidth::Full)
            ->columns([
                Tables\Columns\TextColumn::make('servidor.matricula')
                    ->label('Mat. Servidor Afastado')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.nome')
                    ->label('Nome Servidor Afastado')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('substituto.matricula')
                    ->label('Mat. Servidor Substituto')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                Tables\Columns\TextColumn::make('substituto.nome')
                    ->label('Nome Servidor Substituto')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipoAtestado.nome')
                    ->label('Tipo de Afastamento')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.setores.nome')
                    ->label('Local de Trabalho')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.lotacao.cargo.nome')
                    ->label('Cargo')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.lotacao.cargo.regimeContratual.nome')
                    ->label('Regime Contratual')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantidade_dias')
                    ->label('Qtd Dias')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),


                Tables\Columns\TextColumn::make('data_inicio')
                    ->label('Data de Início')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_fim')
                    ->label('Data de Fim')
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
                // Filtro por tipo de atestado
                Tables\Filters\SelectFilter::make('tipo_atestado_id')
                    ->label('Tipo de Afastamento')
                    ->relationship('tipoAtestado', 'nome')
                    ->preload()
                    ->multiple(),

                // Filtro por setor
                Tables\Filters\SelectFilter::make('setores')
                    ->label('Setor')
                    ->relationship('servidor.setores', 'nome')
                    ->preload()
                    ->multiple(),

                // Filtro por período pré-definido
                Tables\Filters\SelectFilter::make('periodo')
                    ->label('Período')
                    ->options([
                        'esta_semana'     => 'Esta Semana',
                        'este_mes'        => 'Este Mês',
                        'ultimo_mes'      => 'Último Mês',
                        'este_semestre'   => 'Este Semestre',
                        'ultimo_semestre' => 'Último Semestre',
                        'este_ano'        => 'Este Ano',
                        'ultimo_ano'      => 'Último Ano',
                        'geral'           => 'Geral',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if (!$value || $value === 'geral') {
                            return;
                        }

                        $now = now();

                        match ($value) {
                            'esta_semana' => $query->whereBetween('data_inicio', [$now->startOfWeek(), $now->endOfWeek()]),
                            'este_mes' => $query->whereBetween('data_inicio', [$now->startOfMonth(), $now->endOfMonth()]),
                            'ultimo_mes' => $query->whereBetween('data_inicio', [
                                $now->copy()->subMonth()->startOfMonth(),
                                $now->copy()->subMonth()->endOfMonth()
                            ]),
                            'este_semestre' => $query->whereBetween('data_inicio', [
                                $now->month <= 6 ? $now->startOfYear() : $now->copy()->month(7)->startOfMonth(),
                                $now->month <= 6 ? $now->copy()->month(6)->endOfMonth() : $now->endOfYear(),
                            ]),
                            'ultimo_semestre' => $query->whereBetween('data_inicio', [
                                $now->month <= 6
                                    ? $now->copy()->subYear()->month(7)->startOfMonth()
                                    : $now->copy()->startOfYear(),
                                $now->month <= 6
                                    ? $now->copy()->subYear()->endOfYear()
                                    : $now->copy()->month(6)->endOfMonth(),
                            ]),
                            'este_ano' => $query->whereBetween('data_inicio', [$now->startOfYear(), $now->endOfYear()]),
                            'ultimo_ano' => $query->whereBetween('data_inicio', [
                                $now->copy()->subYear()->startOfYear(),
                                $now->copy()->subYear()->endOfYear()
                            ]),
                            default => null,
                        };
                    }),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAtestados::route('/'),
            'create' => Pages\CreateAtestado::route('/create'),
            'edit' => Pages\EditAtestado::route('/{record}/edit'),
        ];
    }
}
