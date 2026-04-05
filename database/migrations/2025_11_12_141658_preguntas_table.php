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
        // database/migrations/xxxx_create_preguntas_table.php
        Schema::create('preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 10); // '01', '02', etc.
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('imagen_descripcion')->nullable();
            $table->text('pregunta');
            $table->string('imagen_pregunta')->nullable();
            $table->string('tipo_interaccion'); // Ver tipos abajo
            $table->json('configuracion')->nullable();
            $table->json('respuesta_correcta');
            $table->text('explicacion');
            $table->string('imagen_respuesta')->nullable();
            $table->string('nivel'); // I, II, III, IV, V, VI
            $table->string('dificultad'); // Baja, Media, Alta
            $table->string('pais_origen')->nullable();
            $table->string('codigo_tarea')->nullable(); // 2022-DE-06
            $table->timestamps();
        });

        Schema::create('progreso_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pregunta_id')->constrained()->onDelete('cascade');
            $table->json('respuesta_usuario');
            $table->boolean('es_correcta');
            $table->integer('intentos')->default(1);
            $table->timestamp('completada_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
