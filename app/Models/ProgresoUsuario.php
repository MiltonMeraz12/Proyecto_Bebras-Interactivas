<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }
}