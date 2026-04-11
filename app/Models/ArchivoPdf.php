<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArchivoPdf extends Model
{
    protected $table = 'archivos_pdf';

    protected $fillable = [
        'nombre',
        'descripcion',
        'nombre_original',
        'ruta',
        'tamanio',
        'subido_por',
    ];

    protected function casts(): array
    {
        return [
            'tamanio' => 'integer',
        ];
    }

    // Relaciones
    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function conjuntos(): HasMany
    {
        return $this->hasMany(Conjunto::class, 'pdf_id');
    }

    // Utilidades?
    public function urlDescarga(): string
    {
        return Storage::url($this->ruta);
    }

    public function tamanioLegible(): string
    {
        $bytes = $this->tamanio;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        return round($bytes / 1024, 2) . ' KB';
    }
}
