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

class AtestadoResource extends Resource
{
    protected static ?string $model = Atestado::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static ?string $navigationGroup = 'Gerenciamento de Servidores';

    protected static ?string $modelLabel = 'Afastamento';

    protected static ?string $pluralModelLabel = 'Afastamentos';



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
