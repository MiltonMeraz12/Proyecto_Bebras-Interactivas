<x-layouts.app :title="$pdf->nombre">
    <div class="flex flex-col gap-4 p-3 sm:p-4 lg:p-6">
        <div class="flex justify-between items-start flex-wrap gap-3">
            <a href="{{ route('recursos.index') }}"
               class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors"
               wire:navigate>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Recursos
            </a>
        </div>

        <div class="bg-white/95 dark:bg-neutral-900/90 border border-yellow-300/70 dark:border-neutral-700 rounded-3xl shadow-xl px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            {{-- Información del PDF --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-extrabold text-neutral-900 dark:text-white truncate">
                    {{ $pdf->nombre }}
                </h1>
                @if($pdf->descripcion)
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1 line-clamp-2">
                        {{ $pdf->descripcion }}
                    </p>
                @endif
            </div>

            {{-- Botón de descarga --}}
            <div class="flex-shrink-0 w-full sm:w-auto">
                <a href="{{ Storage::url($pdf->ruta) }}"
                download="{{ $pdf->nombre_original }}"
                class="inline-flex items-center justify-center w-full sm:w-auto gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white font-bold text-sm px-6 py-3 rounded-2xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar PDF
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden"
             style="height: 82vh;">
            <iframe src="{{ Storage::url($pdf->ruta) }}"
                    class="w-full h-full"
                    type="application/pdf"
                    title="{{ $pdf->nombre }}">
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <p class="text-neutral-500 mb-4">Tu navegador no puede mostrar este PDF.</p>
                    <a href="{{ Storage::url($pdf->ruta) }}"
                       class="text-pink-600 underline font-semibold">Descárgalo aquí</a>
                </div>
            </iframe>
        </div>
    </div>
</x-layouts.app>