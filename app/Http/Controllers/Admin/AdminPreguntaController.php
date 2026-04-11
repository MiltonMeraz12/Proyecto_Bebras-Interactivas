<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conjunto;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class AdminPreguntaController extends Controller
{
    // Lista de tipos disponibles (la puedes mover a un Enum si prefieres)
    private array $tipos = [
        'seleccion_simple',
        'seleccion_multiple',
        'ordenar',
        'grid_seleccion',
        'emparejar',
        'rellenar',
        'texto_libre',
        'colocar_piezas',
        'colorear_hexagonos',
        'tejer_alfombra',
        'completar',
    ];

    public function create(Conjunto $conjunto): View
    {
        $tipos = $this->tipos;
        $siguienteOrden = $conjunto->preguntas()->max('orden') + 1;
        return view('admin.preguntas.create', compact('conjunto', 'tipos', 'siguienteOrden'));
    }

    public function store(Request $request, Conjunto $conjunto): RedirectResponse
    {
        $data = $request->validate([
            'titulo'              => ['required', 'string', 'max:255'],
            'enunciado'           => ['required', 'string'],
            'tipo_interaccion'    => ['required', 'in:' . implode(',', $this->tipos)],
            'configuracion'       => ['nullable', 'string'], // JSON como texto
            'respuesta_correcta'  => ['required', 'string'], // JSON como texto
            'explicacion'         => ['nullable', 'string'],
            'orden'               => ['required', 'integer', 'min:1'],
            'dificultad'          => ['nullable', 'in:Baja,Media,Alta'],
            'nivel'               => ['nullable', 'string', 'max:10'],
            'codigo_tarea'        => ['nullable', 'string', 'max:50'],
            'pais_origen'         => ['nullable', 'string', 'max:100'],
        ]);

        // Convertir JSON string a array
        $data['configuracion']    = $data['configuracion']
            ? json_decode($data['configuracion'], true)
            : null;
        $data['respuesta_correcta'] = json_decode($data['respuesta_correcta'], true);
        $data['conjunto_id']        = $conjunto->id;
        $data['activa']             = true;

        Pregunta::create($data);

        return redirect()->route('admin.conjuntos.show', $conjunto)
            ->with('success', 'Pregunta agregada correctamente.');
    }

    public function edit(Conjunto $conjunto, Pregunta $pregunta): View
    {
        abort_if($pregunta->conjunto_id !== $conjunto->id, 404);
        $tipos = $this->tipos;
        return view('admin.preguntas.edit', compact('conjunto', 'pregunta', 'tipos'));
    }

    public function update(Request $request, Conjunto $conjunto, Pregunta $pregunta): RedirectResponse
    {
        abort_if($pregunta->conjunto_id !== $conjunto->id, 404);

        $data = $request->validate([
            'titulo'             => ['required', 'string', 'max:255'],
            'enunciado'          => ['required', 'string'],
            'tipo_interaccion'   => ['required', 'in:' . implode(',', $this->tipos)],
            'configuracion'      => ['nullable', 'string'],
            'respuesta_correcta' => ['required', 'string'],
            'explicacion'        => ['nullable', 'string'],
            'orden'              => ['required', 'integer', 'min:1'],
            'dificultad'         => ['nullable', 'in:Baja,Media,Alta'],
            'nivel'              => ['nullable', 'string', 'max:10'],
            'codigo_tarea'       => ['nullable', 'string', 'max:50'],
            'pais_origen'        => ['nullable', 'string', 'max:100'],
        ]);

        $data['configuracion']     = $data['configuracion']
            ? json_decode($data['configuracion'], true)
            : null;
        $data['respuesta_correcta']= json_decode($data['respuesta_correcta'], true);

        $pregunta->update($data);

        return redirect()->route('admin.conjuntos.show', $conjunto)
            ->with('success', 'Pregunta actualizada.');
    }

    public function destroy(Conjunto $conjunto, Pregunta $pregunta): RedirectResponse
    {
        abort_if($pregunta->conjunto_id !== $conjunto->id, 404);
        $pregunta->delete();

        return redirect()->route('admin.conjuntos.show', $conjunto)
            ->with('success', 'Pregunta eliminada.');
    }

    public function toggle(Conjunto $conjunto, Pregunta $pregunta): RedirectResponse
    {
        abort_if($pregunta->conjunto_id !== $conjunto->id, 404);
        $pregunta->update(['activa' => !$pregunta->activa]);

        return back()->with('success', $pregunta->activa ? 'Pregunta activada.' : 'Pregunta desactivada.');
    }
}
