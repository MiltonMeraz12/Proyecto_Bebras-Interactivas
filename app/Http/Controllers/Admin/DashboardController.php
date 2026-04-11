<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conjunto;
use App\Models\ArchivoPdf;
use App\Models\User;
use App\Models\ProgresoUsuario;
use App\Models\SesionConjunto;
use App\Models\Pregunta;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalAlumnos   = User::where('role', 'alumno')->count();
        $totalPdfs      = ArchivoPdf::count();
        $totalConjuntos = Conjunto::count();
        $totalRespuestas= ProgresoUsuario::count();

        $alumnos = User::where('role', 'alumno')
            ->withCount(['progresos'])
            ->with(['sesionesConjunto' => fn($q) => $q->whereNotNull('terminado_en')])
            ->latest()
            ->get();
        
        $conjuntos = Conjunto::withCount(['preguntas', 'sesiones'])
            ->latest()
            ->get();

        $totalPreguntasSistema = Pregunta::where('activa', true)->count();

        return view('admin.dashboard', compact(
            'totalAlumnos', 'totalPdfs', 'totalConjuntos', 'totalRespuestas',
            'alumnos', 'conjuntos', 'totalPreguntasSistema'
        ));
    }

    public function verProgreso($userId): View
    {
        $usuario = \App\Models\User::findOrFail($userId);
        
        abort_if(!$usuario->isAlumno(), 404);

        $sesiones = SesionConjunto::where('user_id', $usuario->id)
            ->with(['conjunto.preguntas'])
            ->latest()
            ->get();

        $progresos = ProgresoUsuario::where('user_id', $usuario->id)
            ->with('pregunta')
            ->get()
            ->keyBy('pregunta_id');

        return view('admin.alumnos.progreso', compact('usuario', 'sesiones', 'progresos'));
    }
    
}