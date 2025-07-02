<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServidorResource\Pages;
use App\Models\Servidor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Carbon\Carbon;

use Filament\Tables\Actions\Action;

class ServidorResource extends Resource
{
    protected static ?string $model = Servidor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static ?string $modelLabel = 'Servidor';

    protected static ?string $navigationGroup = "Gerenciamento de Servidores";

    public static ?string $pluralModelLabel = 'Servidores';

    public static ?string $slug = 'servidores';

    public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with(['setores', 'cargaHoraria', 'lotacao']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('matricula')
                    ->unique(ignoreRecord: true)
                    ->label('Matricula')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('nome')
                    ->label('Nome')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->unique(ignoreRecord: true)
                    ->email()
                    ->required(),
                Forms\Components\Select::make('cargo_id')
                    ->label('Cargo')
                    ->preload()
                    ->relationship('cargo', 'nome')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nome} - {$record->regimeContratual->nome}")
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('turno_id')
                    ->label('Turno')
                    ->preload()
                    ->relationship('turno', 'nome')
                    ->required(),

                Forms\Components\Select::make('setores')
                    ->label('Locais de Trabalho')
                    ->relationship('setores', 'nome')
                    ->preload()
                    ->multiple()
                    ->required(),

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
                            ->pluck('nome', 'id')
                            ->map(function ($nome, $id) {
                                $lotacao = \App\Models\Lotacao::find($id);
                                return "{$lotacao->codigo} - {$lotacao->cargo->nome} {$lotacao->cargo->regimeContratual->nome} |{$lotacao->setor->nome}";
                            });
                    })
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        return "{$record->codigo} - {$record->cargo->nome} {$record->cargo->regimeContratual->nome} | {$record->setor->nome}";
                    })
                    ->required(),

                Forms\Components\DatePicker::make('data_admissao')
                    ->label('Data de Admissão')
                    ->required(),

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
                                            ->label('Saída para Almoço')
                                            ->seconds(false),

                                        Forms\Components\TimePicker::make('entrada_intervalo')
                                            ->label('Retorno do Almoço')
                                            ->seconds(false),

                                        Forms\Components\TimePicker::make('saida')
                                            ->label('Saída')
                                            ->required()
                                            ->seconds(false),
                                    ])
                            ])
                    ])
                    ->columns(1)
                    ->collapsed(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('setor_id')
                    ->label('Local de Trabalho')
                    ->getStateUsing(
                        fn($record) =>
                        $record->setores && $record->setores->count() > 0
                            ? $record->setores->pluck('nome')->join(', ')
                            : null
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lotacao.codigo')
                    ->label('Codigo da Lotação')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lotacao.nome')
                    ->label('Nome da Lotação')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lotacao.setor.nome')
                    ->label('Local de Lotação')
                    ->toggleable(isToggledHiddenByDefault: true)
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

                Tables\Columns\TextColumn::make('cargo.nome')
                    ->sortable()
                    ->label('Cargo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('cargo.regimeContratual.nome')
                    ->sortable()
                    ->label('Regime Contratual')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

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
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y'))
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
                SelectFilter::make('cargo_id')
                    ->label('Cargo')
                    ->preload()
                    ->multiple()
                    ->options(function () {
                        return \App\Models\Cargo::with('regimeContratual')->get()->mapWithKeys(function ($cargo) {
                            return [
                                $cargo->id => "{$cargo->nome} - {$cargo->regimeContratual->nome}"
                            ];
                        })->toArray();
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
                            return [
                                $lotacao->id => "{$lotacao->codigo} - {$lotacao->nome} ({$lotacao->setor->nome})"
                            ];
                        })->toArray();
                    }),

                Tables\Filters\Filter::make('entrada_range')
                    ->form([
                        Forms\Components\TimePicker::make('entrada_min')
                            ->seconds(false)
                            ->label('Entrada Mínima'),
                        Forms\Components\TimePicker::make('entrada_max')
                            ->seconds(false)
                            ->label('Entrada Máxima'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['entrada_min'])) {
                            $query->whereHas('cargaHoraria', fn($q) => $q->where('entrada', '>=', $data['entrada_min']));
                        }
                        if (!empty($data['entrada_max'])) {
                            $query->whereHas('cargaHoraria', fn($q) => $q->where('entrada', '<=', $data['entrada_max']));
                        }
                    }),

                Tables\Filters\Filter::make('search')
                    ->form([
                        Forms\Components\TextInput::make('search')->label('Buscar'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['search'])) return;

                        $search = $data['search'];

                        $query->where(function ($q) use ($search) {
                            $q->where('nome', 'like', "%{$search}%")
                                ->orWhereHas('cargo', fn($q) => $q->where('nome', 'like', "%{$search}%"))
                                ->orWhereHas('turno', fn($q) => $q->where('nome', 'like', "%{$search}%"))
                                ->orWhereHas('cargo.regimeContratual', fn($q) => $q->where('nome', 'like', "%{$search}%"));
                        });
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
