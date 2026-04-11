<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchivoPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class PdfController extends Controller
{
    public function index(): View
    {
        $pdfs = ArchivoPdf::with('subidoPor')
            ->withCount('conjuntos')
            ->latest()
            ->get();

        return view('admin.pdfs.index', compact('pdfs'));
    }

    public function create(): View
    {
        return view('admin.pdfs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'archivo'     => ['required', 'file', 'mimes:pdf', 'max:20480'], // 20 MB
        ]);

        $archivo = $request->file('archivo');
        $ruta    = $archivo->store('pdfs', 'public');

        ArchivoPdf::create([
            'nombre'          => $request->nombre,
            'descripcion'     => $request->descripcion,
            'nombre_original' => $archivo->getClientOriginalName(),
            'ruta'            => $ruta,
            'tamanio'         => $archivo->getSize(),
            'subido_por'      => auth()->id(),
        ]);

        return redirect()->route('admin.pdfs.index')
            ->with('success', 'PDF subido correctamente.');
    }

    public function show(ArchivoPdf $archivoPdf): View
    {
        $archivoPdf->load(['subidoPor', 'conjuntos']);
        return view('admin.pdfs.show', compact('archivoPdf'));
    }

    public function destroy(ArchivoPdf $archivoPdf): RedirectResponse
    {
        // No borrar si tiene conjuntos asociados
        if ($archivoPdf->conjuntos()->exists()) {
            return back()->with('error', 'No puedes eliminar este PDF porque tiene conjuntos asociados. Desvincula o elimina los conjuntos primero.');
        }

        Storage::disk('public')->delete($archivoPdf->ruta);
        $archivoPdf->delete();

        return redirect()->route('admin.pdfs.index')
            ->with('success', 'PDF eliminado correctamente.');
    }
}
