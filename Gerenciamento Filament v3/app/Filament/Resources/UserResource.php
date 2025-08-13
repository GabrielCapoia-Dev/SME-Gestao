<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Servidor;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Setor;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class UserResource extends Resource
{

    public static function getNavigationBadge(): ?string
    {
        $value = (string) static::getModel()::count();

        if ($value > 0) {
            return $value;
        }
        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Quantidade de usuários cadastrados';
    }

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static ?string $modelLabel = 'Usuário';

    protected static ?string $navigationGroup = "Administrativo";

    public static ?string $pluralModelLabel = 'Usuários';

    public static ?string $slug = 'usuarios';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome de usuário')
                    ->required()
                    ->disabled(function (Get $get, ?User $record): bool {
                        $hasServidorNoRegistro = $record?->servidor()->exists() ?? false;
                        return filled($get('servidor_id')) || $hasServidorNoRegistro;
                    })
                    ->dehydrated(true),

                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->unique(ignoreRecord: true)
                    ->email()
                    ->required()
                    ->disabled(function (Get $get, ?User $record): bool {
                        $hasServidorNoRegistro = $record?->servidor()->exists() ?? false;
                        return filled($get('servidor_id')) || $hasServidorNoRegistro;
                    })
                    ->dehydrated(true),


                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn($state) => \Illuminate\Support\Facades\Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create'),

                Forms\Components\Select::make('role')
                    ->label('Nível de acesso')
                    ->relationship('roles', 'name', function (\Illuminate\Database\Eloquent\Builder $query) {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if (!$user || $user->hasRole('Admin')) {
                            return $query;
                        }
                        return $query->where('name', '!=', 'Admin');
                    })
                    ->preload()
                    ->required(),

                Forms\Components\Toggle::make('email_approved')
                    ->label('Verificação de acesso')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->required()
                    ->default(true),

                Forms\Components\Select::make('servidor_id')
                    ->label('Servidor vinculado')
                    ->options(
                        fn() => Servidor::whereNull('user_id')
                            ->get(['id', 'matricula', 'nome'])
                            ->mapWithKeys(fn($s) => [
                                $s->id => "[{$s->matricula}] {$s->nome}",
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search): array {
                        return Servidor::query()
                            ->whereNull('user_id')
                            ->where(function ($q) use ($search) {
                                $q->where('nome', 'like', "%{$search}%")
                                    ->orWhere('matricula', 'like', "%{$search}%");
                            })
                            ->limit(50)
                            ->get(['id', 'matricula', 'nome'])
                            ->mapWithKeys(fn($s) => [
                                $s->id => "[{$s->matricula}] {$s->nome}",
                            ])
                            ->toArray();
                    })
                    ->helperText('Só é possível vincular um servidor que ainda não esteja associado a outro usuário.')
                    ->native(false)
                    ->visibleOn('create')
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state): void {
                        if (blank($state)) {
                            $set('name', null);
                            $set('email', null);
                            $set('setor_id', null);
                            return;
                        }

                        $servidor = Servidor::with(['setores', 'lotacao.setor'])->find($state);

                        // auto-fill name/email
                        $set('name', $servidor?->nome);
                        $set('email', $servidor?->email);

                        // setor: primeiro do pivot, senão o da lotação
                        $setorId = $servidor?->setores?->first()?->id
                            ?? $servidor?->lotacao?->setor?->id
                            ?? null;

                        $set('setor_id', $setorId);
                    })
                    ->columnSpanFull(),

                Forms\Components\Select::make('setor_id')
                    ->label('Setor')
                    ->options(fn() => Setor::query()->pluck('nome', 'id'))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->nullable()
                    ->required(function (Get $get, ?User $record): bool {
                        $hasServidorNoRegistro = $record?->servidor()->exists() ?? false;
                        return ! $hasServidorNoRegistro && blank($get('servidor_id'));
                    })
                    ->disabled(function (Get $get, ?User $record): bool {
                        $hasServidorNoRegistro = $record?->servidor()->exists() ?? false;
                        return filled($get('servidor_id')) || $hasServidorNoRegistro;
                    })
                    ->helperText(function (Get $get, ?User $record): string {
                        $hasServidorNoRegistro = $record?->servidor()->exists() ?? false;
                        return (filled($get('servidor_id')) || $hasServidorNoRegistro)
                            ? 'Setor definido pelo Servidor e bloqueado para edição.'
                            : 'Selecione um setor.';
                    })
                    ->columnSpanFull()
                    ->visibleOn('create'),


                Forms\Components\Section::make('Vínculo com Servidor')
                    ->icon('heroicon-o-identification')
                    ->description('Servidor vinculado a este usuário')
                    ->schema([

                        Forms\Components\Placeholder::make('servidor_nome_ro')
                            ->label('Servidor vinculado')
                            ->content(function (?User $record): string {
                                $s = $record?->servidor;
                                if (! $s) {
                                    return '—';
                                }

                                $matricula = $s->matricula ?? 's/ matrícula';
                                $nome = $s->nome ?? 's/ nome';

                                return "[{$matricula}] {$nome}";
                            })
                            ->extraAttributes(['class' => 'text-base font-medium'])
                            ->visibleOn('edit'),

                        Forms\Components\Placeholder::make('servidor_setor_ro')
                            ->label('Local de Trabalho')
                            ->content(function (?User $record) {
                                $servidor = $record?->servidor;

                                if (! $servidor) {
                                    return '—';
                                }

                                // Prefer setores via pivot
                                if ($servidor->setores && $servidor->setores->isNotEmpty()) {
                                    return $servidor->setores->pluck('nome')->join(', ');
                                }

                                // Fallback: setor da lotação
                                return $servidor->lotacao?->setor?->nome ?? '—';
                            })
                            ->extraAttributes(['class' => 'text-base']),
                    ])
                    ->columns(3)
                    ->visibleOn('edit') // mostra só no editar
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome de usuário')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('servidor.nome')
                    ->label('Servidor')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('servidor_setor')
                    ->label('Setor do Servidor')
                    ->getStateUsing(function (\App\Models\User $record): ?string {
                        $servidor = $record->servidor;
                        if (! $servidor) {
                            return null;
                        }

                        // Prefer: setores do pivot
                        if ($servidor->setores && $servidor->setores->isNotEmpty()) {
                            return $servidor->setores->pluck('nome')->join(', ');
                        }

                        // Fallback: setor da lotação
                        return $servidor->lotacao?->setor?->nome ?? null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Nivel de acesso')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Verificado em')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->email_approved) {
                            return '--/--/-- --:--:--';
                        }

                        return $state ? $state->format('d/m/Y H:i:s') : '-';
                    }),

                Tables\Columns\ToggleColumn::make('email_approved')
                    ->label('Verificação de Acesso')
                    ->sortable()
                    ->disabled(function ($record) {
                        $user = Auth::user();

                        // Desativa o toggle se for o próprio usuário
                        return $user && $record && $user->id === $record->id;
                    })
                    ->visible(function () {
                        /** @var \App\Models\User|null $user */
                        $user = Auth::user();

                        // Se não estiver autenticado, esconde
                        if (!$user) {
                            return false;
                        }

                        // Mostra só para Admin
                        return $user->hasRole('Admin');
                    })
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->columnSpan(1),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function () {
                            /** @var \App\Models\User|null $user */
                            $user = Auth::user();

                            // Se não estiver autenticado, esconde
                            if (!$user) {
                                return false;
                            }

                            // Mostra só para Admin
                            return $user->hasRole('Admin');
                        }),
                ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['servidor.setores', 'servidor.lotacao.setor']);

        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && ! $user->hasRole('Admin')) {
            $query->whereHas('roles', fn($q) => $q->where('name', '!=', 'Admin'));
        }

        return $query;
    }
}
