<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchivoImagen extends Model
{
    protected $table = 'archivos_imagen';

    protected $fillable = [
        'nombre',
        'nombre_original',
        'ruta',
        'alt',
        'tamanio',
        'mime_type',
        'subida_por',
    ];

    protected function casts(): array
    {
        return ['tamanio' => 'integer'];
    }

    public function subidaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subida_por');
    }

    public function url(): string
    {
        return Storage::url($this->ruta);
    }

    public function tamanioLegible(): string
    {
        $b = $this->tamanio;
        if ($b >= 1048576) return round($b / 1048576, 2) . ' MB';
        return round($b / 1024, 2) . ' KB';
    }
}