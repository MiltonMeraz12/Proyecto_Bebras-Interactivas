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
        Schema::create('preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conjunto_id')->constrained('conjuntos')->onDelete('cascade');
            $table->unsignedInteger('orden')->default(0);
            $table->text('titulo');
            $table->text('enunciado');
            $table->string('imagen_enunciado')->nullable();
            $table->string('tipo_interaccion');              // seleccion_simple, ordenar, etc.
            $table->json('configuracion')->nullable();        // Opciones, imágenes, parámetros del tipo
            $table->json('respuesta_correcta');
            $table->text('explicacion')->nullable();
            $table->string('imagen_explicacion')->nullable();
            
            // Metadata del origen Bebras
            $table->string('codigo_tarea')->nullable();
            $table->string('pais_origen')->nullable();
            $table->string('nivel')->nullable();             // I, II, III...
            $table->string('dificultad')->nullable();        // Baja, Media, Alta
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas');
    }
};
