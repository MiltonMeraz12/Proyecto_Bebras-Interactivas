<x-layouts.app :title="__('Recursos PDF')">
    <div class="flex flex-col gap-4 sm:gap-6 p-3 sm:p-4 lg:p-6">

        <div class="bg-white/95 dark:bg-neutral-900/90 border border-yellow-300/70 dark:border-neutral-700 rounded-3xl shadow-xl px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                Recursos PDF 📄
            </h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-300 font-medium mt-1">
                Aquí puedes visualizar y descargar todos los materiales disponibles.
            </p>
        </div>

        @forelse($pdfs as $pdf)
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-2xl flex-shrink-0">
                            📄
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-neutral-900 dark:text-white truncate">
                                {{ $pdf->nombre }}
                            </h2>
                            @if($pdf->descripcion)
                                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5 line-clamp-2">
                                    {{ $pdf->descripcion }}
                                </p>
                            @endif
                            <p class="text-xs text-neutral-400 mt-1">
                                {{ $pdf->tamanioLegible() }} ·
                                Subido el {{ $pdf->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2 flex-shrink-0">
                        <a href="{{ route('recursos.ver', $pdf) }}"
                           class="inline-flex items-center gap-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 font-semibold px-4 py-2 rounded-2xl text-sm hover:shadow-md transition-all"
                           wire:navigate>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver
                        </a>
                        <a href="{{ Storage::url($pdf->ruta) }}"
                           download="{{ $pdf->nombre_original }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white font-semibold px-4 py-2 rounded-2xl text-sm shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Descargar
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 p-12 text-center">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-neutral-500 dark:text-neutral-400 font-medium">No hay PDFs disponibles aún.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>