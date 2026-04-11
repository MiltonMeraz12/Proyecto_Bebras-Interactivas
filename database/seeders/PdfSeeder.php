<?php

namespace Database\Seeders;

use App\Models\ArchivoPdf;
use App\Models\User;
use Illuminate\Database\Seeder;

class PdfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        ArchivoPdf::create([
            'nombre'          => 'Guía de Soluciones Primavera 2025',
            'descripcion'     => 'Guía oficial con las 27 tareas del Reto Bebras MX Primavera 2025.',
            'nombre_original' => 'Guia_Soluciones_Prim2025.pdf',
            'ruta'            => 'pdfs/placeholder.pdf', // Se reemplaza al subir el real
            'tamanio'         => 0,
            'subido_por'      => $admin->id,
        ]);
    }
}
