<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\SetorResource\Pages;
use App\Filament\Resources\SetorResource\RelationManagers;
use App\Models\Setor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\MaxWidth;

class SetorResource extends Resource
{
    protected static ?string $model = Setor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'RH';

    public static ?string $modelLabel = 'Local de Trabalho';

    public static ?string $pluralModelLabel = 'Locais de Trabalho';

    public static ?string $slug = 'locais-de-trabalho';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('turmas');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),

                Forms\Components\TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\Section::make('Turmas')
                    ->description('Selecione as turmas que pertencem a esta Escola ou CMEI. Você pode escolher múltiplas turmas.')
                    ->schema([
                        Forms\Components\Select::make('turmas')
                            ->label('Turmas')
                            ->options(function () {
                                $turmas = \App\Models\Turma::pluck('nome', 'id');

                                if ($turmas->isEmpty()) {
                                    return [
                                        '' => '⚠️ Cadastre uma turma primeiro',
                                    ];
                                }

                                return $turmas;
                            })
                            ->disabled(fn() => \App\Models\Turma::count() === 0)
                            ->multiple()
                            ->searchable()
                            ->preload(),
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
            ->filtersFormColumns(5)
            ->filtersFormWidth(MaxWidth::Medium)
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('turmas')
                    ->label('Turmas')
                    ->getStateUsing(function ($record) {
                        $turmas = $record->turmas?->pluck('nome')->filter();

                        return $turmas->isNotEmpty()
                            ? $turmas->join(', ')
                            : '⚠️ Não há turmas cadastradas';
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('telefone')
                    ->label('Telefone')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\Filter::make('tipo_escola')
                    ->form([
                        Forms\Components\CheckboxList::make('tipos')
                            ->label(false)
                            ->options([
                                'C.M.E.I' => 'C.M.E.I',
                                'Escola' => 'Escola',
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['tipos'])) {
                            return;
                        }

                        $query->where(function ($q) use ($data) {
                            foreach ($data['tipos'] as $tipo) {
                                $q->orWhere('nome', 'like', "%{$tipo}%");
                            }
                        });
                    })
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
            'index' => Pages\ManageSetors::route('/'),
        ];
    }
}
