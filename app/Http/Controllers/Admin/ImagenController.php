<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchivoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ImagenController extends Controller
{
    public function index(Request $request): View
    {
        $imagenes = ArchivoImagen::with('subidaPor')
            ->latest()
            ->get();

        return view('admin.imagenes.index', compact('imagenes'));
    }

    public function create(): View
    {
        return view('admin.imagenes.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'nombre'  => ['required', 'string', 'max:255'],
            'alt'     => ['nullable', 'string', 'max:255'],
            'archivo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'], // 5 MB
        ]);

        $archivo = $request->file('archivo');
        $ruta    = $archivo->store('imagenes', 'public');

        ArchivoImagen::create([
            'nombre'          => $request->nombre,
            'nombre_original' => $archivo->getClientOriginalName(),
            'ruta'            => $ruta,
            'alt'             => $request->alt ?? $request->nombre,
            'tamanio'         => $archivo->getSize(),
            'mime_type'       => $archivo->getMimeType(),
            'subida_por'      => auth()->id(),
        ]);

        // Si viene de un formulario de pregunta, redirigir al índice con mensaje
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'url'     => Storage::url($ruta),
                'ruta'    => $ruta,
            ]);
        }

        return redirect()->route('admin.imagenes.index')
            ->with('success', 'Imagen subida correctamente.');
    }

    public function show(ArchivoImagen $imagen): View
    {
        /** @var view-string $view */
        $view = 'admin.imagenes.show';
        
        return view($view, compact('imagen'));
    }

    // API para el selector de imágenes en formularios de preguntas
    public function api(): JsonResponse
    {
        $imagenes = ArchivoImagen::latest()->get()->map(fn($img) => [
            'id'     => $img->id,
            'nombre' => $img->nombre,
            'url'    => $img->url(),
            'ruta'   => $img->ruta,
            'alt'    => $img->alt,
        ]);

        return response()->json($imagenes);
    }

    public function destroy(ArchivoImagen $imagen): RedirectResponse
    {
        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        return redirect()->route('admin.imagenes.index')
            ->with('success', 'Imagen eliminada.');
    }
}