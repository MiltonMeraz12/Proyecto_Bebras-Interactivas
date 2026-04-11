<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos_imagen', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');                    // Nombre descriptivo
            $table->string('nombre_original');           // Nombre real del archivo
            $table->string('ruta');                      // storage/app/public/imagenes/...
            $table->string('alt')->nullable();           // Texto alternativo
            $table->unsignedBigInteger('tamanio');       // Bytes
            $table->string('mime_type', 50);             // image/png, image/jpeg, etc.
            $table->foreignId('subida_por')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_imagen');
    }
};