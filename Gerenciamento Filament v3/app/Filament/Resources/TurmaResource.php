<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\TurmaResource\Pages;
use App\Filament\Resources\TurmaResource\RelationManagers;
use App\Models\Turma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TurmaResource extends Resource
{
    protected static ?string $model = Turma::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = "Gerenciamento Escolar";

    public static ?string $modelLabel = 'Turma';

    public static ?string $pluralModelLabel = 'Turmas';

    public static ?string $slug = 'turmas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nome_turma_id')
                    ->label('Nome')
                    ->relationship('nomeTurma', 'nome')
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('sigla_turma_id')
                    ->label('Sigla')
                    ->relationship('siglaTurma', 'nome')
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('descricao')
                    ->label('Descrição'),
                Forms\Components\Select::make('setor_id')
                    ->label('Nome da Escola / CMEI')
                    ->relationship('setor', 'nome')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomeTurma.nome')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('siglaTurma.nome')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('setor.nome')
                    ->label('Escola / CMEI')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->sortable()
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
            'index' => Pages\ManageTurmas::route('/'),
        ];
    }
}
