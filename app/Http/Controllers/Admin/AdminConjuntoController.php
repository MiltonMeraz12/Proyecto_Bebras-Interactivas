<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchivoPdf;
use App\Models\Conjunto;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class AdminConjuntoController extends Controller
{
    public function index(): View
    {
        $conjuntos = Conjunto::with(['pdf', 'creadoPor'])
            ->withCount(['preguntas', 'sesiones'])
            ->latest()
            ->get();

        return view('admin.conjuntos.index', compact('conjuntos'));
    }

    public function create(): View
    {
        $pdfs = ArchivoPdf::orderBy('nombre')->get();
        return view('admin.conjuntos.create', compact('pdfs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'pdf_id'      => ['nullable', 'exists:archivos_pdf,id'],
        ]);

        Conjunto::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'pdf_id'      => $request->pdf_id,
            'creado_por'  => auth()->id(),
            'activo'      => true,
        ]);

        return redirect()->route('admin.conjuntos.index')
            ->with('success', 'Conjunto creado correctamente.');
    }

    public function show(Conjunto $conjunto)
    {
        $conjunto->load(['pdf', 'preguntas' => fn($q) => $q->orderBy('orden'), 'sesiones.user']);
        return view('admin.conjuntos.show', compact('conjunto'));
    }

    public function edit(Conjunto $conjunto)
    {
        $pdfs = ArchivoPdf::orderBy('nombre')->get();
        return view('admin.conjuntos.edit', compact('conjunto', 'pdfs'));
    }

    public function update(Request $request, Conjunto $conjunto): RedirectResponse
    {
        $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'pdf_id'      => ['nullable', 'exists:archivos_pdf,id'],
        ]);

        $conjunto->update($request->only('nombre', 'descripcion', 'pdf_id'));

        return redirect()->route('admin.conjuntos.show', $conjunto)
            ->with('success', 'Conjunto actualizado.');
    }

    public function destroy(Conjunto $conjunto): RedirectResponse
    {
        $conjunto->delete(); // cascade elimina preguntas
        return redirect()->route('admin.conjuntos.index')
            ->with('success', 'Conjunto eliminado.');
    }

    public function toggle(Conjunto $conjunto): RedirectResponse
    {
        $conjunto->update(['activo' => !$conjunto->activo]);

        return back()->with('success', $conjunto->activo ? 'Conjunto activado.' : 'Conjunto desactivado.');
    }
}
