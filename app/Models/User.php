<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

     protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAlumno(): bool
    {
        return $this->role === 'alumno';
    }



    // Relaciones
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgresoUsuario, $this>
     */
    public function progresos(): HasMany
    {
        return $this->hasMany(ProgresoUsuario::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SesionConjunto, $this>
     */
    public function sesionesConjunto(): HasMany
    {
        return $this->hasMany(SesionConjunto::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ArchivoPdf, $this>
     */
    public function pdfsSubidos(): HasMany
    {
        return $this->hasMany(ArchivoPdf::class, 'subido_por');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Conjunto, $this>
     */
    public function conjuntosCreados(): HasMany
    {
        return $this->hasMany(Conjunto::class, 'creado_por');
    }


    // Utilidades?
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
