<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_approved',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'email_verified_at', 'email_approved']);
    }

    public function socialiteUsers(): HasMany
    {
        return $this->hasMany(SocialiteUser::class);
    }

    /**
     * Check if the user can access the Filament panel.
     *
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->email_approved) {
            return true;
        }

        throw new \App\Exceptions\AcessoNegadoLogin(
            'Acesso negado. Entre em contato com o administrador.'
        );
    }


    protected static function booted()
    {

        parent::booted();

        static::deleting(function ($user) {
            $user->socialiteUsers()->delete();
        });

        static::updating(function ($user) {
            if (
                $user->isDirty('email_approved') &&
                $user->email_approved &&
                is_null($user->getOriginal('email_verified_at'))
            ) {
                $user->email_verified_at = now();
            }
        });
    }

    public function servidor()
    {
        return $this->hasOne(Servidor::class);
    }
}
