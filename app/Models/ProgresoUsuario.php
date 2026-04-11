<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresoUsuario extends Model
{
    protected $table = 'progreso_usuarios';
    
    protected $fillable = [
        'user_id', 'pregunta_id', 'respuesta_usuario',
        'es_correcta', 'intentos', 'completada_at'
    ];

    protected $casts = [
        'respuesta_usuario' => 'array',
        'es_correcta' => 'boolean',
        'completada_at' => 'datetime',
        'intentos' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Pregunta, $this>
     */
    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class);
    }
}