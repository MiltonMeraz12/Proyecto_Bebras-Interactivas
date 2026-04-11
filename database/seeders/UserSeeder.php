<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador Test',
            'email' => 'admin@bebras.mx',
            'password' => Hash::make('temporal1'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Alumno Test',
            'email' => 'alumno@bebras.mx',
            'password' => Hash::make('temporal1'),
            'role' => 'alumno',
        ]);

    }
}
