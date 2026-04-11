<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conjunto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'pdf_id',
        'creado_por',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    // Scopes
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('activo', true);
    }

    // Relaciones
    public function pdf(): BelongsTo
    {
        return $this->belongsTo(ArchivoPdf::class, 'pdf_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Pregunta, $this>
     */
    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class)->orderBy('orden');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SesionConjunto, $this>
     */
    public function sesiones(): HasMany
    {
        return $this->hasMany(SesionConjunto::class);
    }

    // Utilidades
    public function totalPreguntas(): int
    {
        return $this->preguntas()->where('activa', true)->count();
    }
}
