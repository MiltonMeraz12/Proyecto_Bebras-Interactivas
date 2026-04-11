<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method \Illuminate\Database\Eloquent\Builder|static activas()
 */
class Pregunta extends Model
{
    protected $fillable = [
        'conjunto_id',
        'orden',
        'titulo',
        'enunciado',
        'imagen_enunciado',
        'tipo_interaccion',
        'configuracion',
        'respuesta_correcta',
        'explicacion',
        'imagen_explicacion',
        'codigo_tarea',
        'pais_origen',
        'nivel',
        'dificultad',
        'activa',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'respuesta_correcta' => 'array',
        'activa' => 'boolean',
        'orden' => 'integer',
    ];

    // Scopes
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivas($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('activa', true);
    }

    // Relaciones
    public function conjunto(): BelongsTo
    {
        return $this->belongsTo(Conjunto::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgresoUsuario, $this>
     */
    public function progresos(): HasMany
    {
        return $this->hasMany(ProgresoUsuario::class);
    }
}