<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\ConjuntoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\AdminConjuntoController;
use App\Http\Controllers\Admin\AdminPreguntaController;
use App\Http\Controllers\Admin\ImagenController;

// Principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Redirigir según rol después del login
Route::get('dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('conjuntos.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Autenticadas
Route::middleware(['auth'])->group(function () {
    
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
    Volt::route('settings/two-factor', 'settings.two-factor')->name('two-factor.show');

    Route::get('/conjuntos', [ConjuntoController::class, 'index'])
        ->name('conjuntos.index');

    Route::get('/conjuntos/{conjunto}', [ConjuntoController::class, 'show'])
        ->name('conjuntos.show');

    // Iniciar sesión en un conjunto
    Route::post('/conjuntos/{conjunto}/iniciar', [ConjuntoController::class, 'iniciar'])
        ->name('conjuntos.iniciar');

    // ─── Preguntas dentro de un conjunto ─────────────────────────
    Route::get('/conjuntos/{conjunto}/preguntas/{pregunta}', [PreguntaController::class, 'show'])
        ->name('preguntas.show');

    Route::post('/conjuntos/{conjunto}/preguntas/{pregunta}/verificar', [PreguntaController::class, 'verificar'])
        ->name('preguntas.verificar');

    // Finalizar conjunto
    Route::patch('/conjuntos/{conjunto}/finalizar', [ConjuntoController::class, 'finalizar'])
        ->name('conjuntos.finalizar');

    // Resultados del alumno al terminar
    Route::get('/conjuntos/{conjunto}/resultados', [ConjuntoController::class, 'resultados'])
        ->name('conjuntos.resultados');

    // Página de recursos (PDFs)
    Route::get('/recursos', function () {
        $pdfs = \App\Models\ArchivoPdf::latest()->get();
        return view('recursos.index', compact('pdfs'));
    })->name('recursos.index');

    // Ver PDF individual
    Route::get('/recursos', function () {
        $pdfs = \App\Models\ArchivoPdf::latest()->get();
        return view('recursos.index', compact('pdfs'));
    })->name('recursos.index');

    Route::get('/recursos/{archivoPdf}', function (\App\Models\ArchivoPdf $archivoPdf) {
        return view('recursos.ver', ['pdf' => $archivoPdf]);
    })->name('recursos.ver');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/alumnos/{usuario}/progreso', [DashboardController::class, 'verProgreso'])
        ->name('alumnos.progreso');

    // PDFs - Recursos
    Route::get('/pdfs',                [PdfController::class, 'index'])  ->name('pdfs.index');
    Route::get('/pdfs/create',         [PdfController::class, 'create']) ->name('pdfs.create');
    Route::post('/pdfs',               [PdfController::class, 'store'])  ->name('pdfs.store');
    Route::get('/pdfs/{archivoPdf}',   [PdfController::class, 'show'])   ->name('pdfs.show');
    Route::delete('/pdfs/{archivoPdf}',[PdfController::class, 'destroy'])->name('pdfs.destroy');

    // Conjuntos (gestión admin)
    Route::get('/conjuntos',                       [AdminConjuntoController::class, 'index'])  ->name('conjuntos.index');
    Route::get('/conjuntos/create',                [AdminConjuntoController::class, 'create']) ->name('conjuntos.create');
    Route::post('/conjuntos',                      [AdminConjuntoController::class, 'store'])  ->name('conjuntos.store');
    Route::get('/conjuntos/{conjunto}',            [AdminConjuntoController::class, 'show'])   ->name('conjuntos.show');
    Route::get('/conjuntos/{conjunto}/edit',       [AdminConjuntoController::class, 'edit'])   ->name('conjuntos.edit');
    Route::patch('/conjuntos/{conjunto}',          [AdminConjuntoController::class, 'update']) ->name('conjuntos.update');
    Route::delete('/conjuntos/{conjunto}',         [AdminConjuntoController::class, 'destroy'])->name('conjuntos.destroy');
    Route::patch('/conjuntos/{conjunto}/toggle',   [AdminConjuntoController::class, 'toggle']) ->name('conjuntos.toggle');

    // Preguntas dentro de un conjunto (gestión admin)
    Route::get('/conjuntos/{conjunto}/preguntas/create',        [AdminPreguntaController::class, 'create']) ->name('preguntas.create');
    Route::post('/conjuntos/{conjunto}/preguntas',              [AdminPreguntaController::class, 'store'])  ->name('preguntas.store');
    Route::get('/conjuntos/{conjunto}/preguntas/{pregunta}/edit',[AdminPreguntaController::class, 'edit'])  ->name('preguntas.edit');
    Route::patch('/conjuntos/{conjunto}/preguntas/{pregunta}',  [AdminPreguntaController::class, 'update']) ->name('preguntas.update');
    Route::delete('/conjuntos/{conjunto}/preguntas/{pregunta}', [AdminPreguntaController::class, 'destroy'])->name('preguntas.destroy');
    Route::patch('/conjuntos/{conjunto}/preguntas/{pregunta}/toggle', [AdminPreguntaController::class, 'toggle'])->name('preguntas.toggle');

    // Imágenes
    Route::get('/imagenes',              [ImagenController::class, 'index'])  ->name('imagenes.index');
    Route::get('/imagenes/create',       [ImagenController::class, 'create']) ->name('imagenes.create');
    Route::post('/imagenes',             [ImagenController::class, 'store'])  ->name('imagenes.store');
    Route::get('/imagenes/{imagen}',     [ImagenController::class, 'show'])   ->name('imagenes.show');
    Route::delete('/imagenes/{imagen}',  [ImagenController::class, 'destroy'])->name('imagenes.destroy');
    Route::get('/imagenes/api/lista',    [ImagenController::class, 'api'])    ->name('imagenes.api');
});