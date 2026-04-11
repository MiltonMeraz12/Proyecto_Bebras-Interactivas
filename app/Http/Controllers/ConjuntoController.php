<?php

namespace App\Http\Controllers;

use App\Models\Conjunto;
use App\Models\SesionConjunto;
use App\Models\ProgresoUsuario;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ConjuntoController extends Controller
{
    // Lista de conjuntos activos para el alumno
    public function index(): View
    {
        $conjuntos = Conjunto::activos()
            ->with(['pdf', 'preguntas'])
            ->withCount(['preguntas' => fn($q) => $q->where('activa', true)])
            ->latest()
            ->get();

        // Sesiones activas del alumno para saber cuáles ya inició/completó
        $sesiones = SesionConjunto::where('user_id', auth()->id())
            ->pluck('terminado_en', 'conjunto_id');
        
        $totalRespondidas = ProgresoUsuario::where('user_id', auth()->id())->count();
        $totalCorrectas   = ProgresoUsuario::where('user_id', auth()->id())->where('es_correcta', true)->count();
        $totalConjuntos   = $conjuntos->count();
        $completados      = $sesiones->filter(fn($v) => $v !== null)->count();

        return view('conjuntos.index', compact(
            'conjuntos', 'sesiones',
            'totalRespondidas', 'totalCorrectas', 'totalConjuntos', 'completados'
        ));
    }

    // Detalle de un conjunto antes de iniciar
    public function show(Conjunto $conjunto): View
    {
        abort_if(!$conjunto->activo, 404);

        $conjunto->load(['pdf', 'preguntas' => fn($q) => $q->activas()->orderBy('orden')]);

        $sesion = SesionConjunto::where('user_id', auth()->id())
            ->where('conjunto_id', $conjunto->id)
            ->first();

        // IDs de preguntas ya respondidas por este alumno en este conjunto
        $preguntasRespondidas = collect();
        if ($sesion) {
            $idPreguntas = $conjunto->preguntas->pluck('id');
            $preguntasRespondidas = ProgresoUsuario::where('user_id', auth()->id())
                ->whereIn('pregunta_id', $idPreguntas)
                ->pluck('pregunta_id');
        }

        return view('conjuntos.show', compact('conjunto', 'sesion', 'preguntasRespondidas'));
    }

    // Iniciar sesión en un conjunto
    public function iniciar(Request $request, Conjunto $conjunto): RedirectResponse
    {
        abort_if(!$conjunto->activo, 403);

        // Crear sesión solo si no existe
        SesionConjunto::firstOrCreate(
            ['user_id' => auth()->id(), 'conjunto_id' => $conjunto->id],
            ['iniciado_en' => now()]
        );

        // Ir a la primera pregunta activa
        $primera = $conjunto->preguntas()->activas()->orderBy('orden')->first();

        if (!$primera) {
            return back()->with('error', 'Este conjunto no tiene preguntas disponibles.');
        }

        return redirect()->route('preguntas.show', [$conjunto, $primera]);
    }

    // Finalizar conjunto y calcular puntuación
    public function finalizar(Request $request, Conjunto $conjunto): RedirectResponse
    {
        $sesion = SesionConjunto::where('user_id', auth()->id())
            ->where('conjunto_id', $conjunto->id)
            ->firstOrFail();

        if ($sesion->estaTerminada()) {
            return redirect()->route('conjuntos.resultados', $conjunto);
        }

        $idPreguntas = $conjunto->preguntas()->activas()->pluck('id');

        $correctas = ProgresoUsuario::where('user_id', auth()->id())
            ->whereIn('pregunta_id', $idPreguntas)
            ->where('es_correcta', true)
            ->count();

        $sesion->update([
            'terminado_en' => now(),
            'puntuacion'   => $correctas,
        ]);

        return redirect()->route('conjuntos.resultados', $conjunto);
    }

    // Vista de resultados al terminar
    public function resultados(Conjunto $conjunto): View
    {
        $sesion = SesionConjunto::where('user_id', auth()->id())
            ->where('conjunto_id', $conjunto->id)
            ->firstOrFail();

        $preguntas = $conjunto->preguntas()->activas()->orderBy('orden')->get();

        $progresos = ProgresoUsuario::where('user_id', auth()->id())
            ->whereIn('pregunta_id', $preguntas->pluck('id'))
            ->get()
            ->keyBy('pregunta_id');

        return view('conjuntos.resultados', compact('conjunto', 'sesion', 'preguntas', 'progresos'));
    }
}
