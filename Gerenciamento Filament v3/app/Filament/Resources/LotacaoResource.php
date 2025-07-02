<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\LotacaoResource\Pages;
use App\Filament\Resources\LotacaoResource\RelationManagers;
use App\Models\Lotacao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LotacaoResource extends Resource
{
    protected static ?string $model = Lotacao::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'RH';

    public static ?string $modelLabel = 'Lotação';

    public static ?string $pluralModelLabel = 'Lotações';

    public static ?string $slug = 'lotacoes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('codigo')
                    ->label('Codigo Lotação')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('descricao')
                    ->label('Descrição')
                    ->maxLength(255),

                Forms\Components\Select::make('setor_id')
                    ->label('Local de Trabalho')
                    ->relationship('setor', 'nome')
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('cargo_id')
                    ->label('Cargo')
                    ->relationship('cargo', 'nome')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nome} - {$record->regimeContratual->nome}")
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Codigo Lotação')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('setor.nome')
                    ->label('Local de Trabalho')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                Tables\Columns\TextColumn::make('cargo.nome')
                    ->sortable()
                    ->label('Cargo')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                Tables\Columns\TextColumn::make('cargo.regimeContratual.nome')
                    ->sortable()
                    ->label('Regime Contratual')
                    ->toggleable(isToggledHiddenByDefault: false)
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
                //
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
            'index' => Pages\ManageLotacaos::route('/'),
        ];
    }
}
