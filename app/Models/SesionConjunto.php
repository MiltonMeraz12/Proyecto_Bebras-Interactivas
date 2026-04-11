<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesionConjunto extends Model
{
    protected $table = 'sesiones_conjunto';

    protected $fillable = [
        'user_id',
        'conjunto_id',
        'iniciado_en',
        'terminado_en',
        'puntuacion',
    ];

    protected function casts(): array
    {
        return [
            'iniciado_en'  => 'datetime',
            'terminado_en' => 'datetime',
            'puntuacion'   => 'integer',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Conjunto, $this>
     */
    public function conjunto(): BelongsTo
    {
        return $this->belongsTo(Conjunto::class);
    }

    public function estaTerminada(): bool
    {
        return $this->terminado_en !== null;
    }
}
