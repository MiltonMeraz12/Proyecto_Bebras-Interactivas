<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sesiones_conjunto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('conjunto_id')
                  ->constrained('conjuntos')
                  ->onDelete('cascade');
            $table->timestamp('iniciado_en')->useCurrent();
            $table->timestamp('terminado_en')->nullable();
            $table->unsignedInteger('puntuacion')->nullable(); // Preguntas correctas al terminar
            $table->timestamps();

            $table->unique(['user_id', 'conjunto_id']); // Un alumno, una sesión por conjunto
        });

        // Registro por pregunta individual
        Schema::create('progreso_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('pregunta_id')
                  ->constrained('preguntas')
                  ->onDelete('cascade');
            $table->json('respuesta_usuario');
            $table->boolean('es_correcta');
            $table->unsignedInteger('intentos')->default(1);
            $table->timestamp('completada_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'pregunta_id']); // Previene duplicados a nivel de BD
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progreso_usuarios');
        Schema::dropIfExists('sesiones_conjunto');
    }
};
