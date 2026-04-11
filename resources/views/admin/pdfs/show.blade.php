<x-layouts.app :title="$archivoPdf->nombre">
    <div class="flex flex-col gap-6 p-4 lg:p-6">

        <div class="flex justify-between items-start flex-wrap gap-4">
            <a href="{{ route('admin.pdfs.index') }}"
               class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors"
               wire:navigate>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a PDFs
            </a>
            <a href="{{ Storage::url($archivoPdf->ruta) }}"
               target="_blank"
               download="{{ $archivoPdf->nombre_original }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 text-white font-bold text-sm px-5 py-2.5 rounded-2xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar PDF
            </a>
        </div>

        {{-- Info --}}
        <div class="bg-white/95 dark:bg-neutral-900/90 border border-yellow-300/70 dark:border-neutral-700 rounded-3xl shadow-xl px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                {{ $archivoPdf->nombre }}
            </h1>
            @if($archivoPdf->descripcion)
                <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ $archivoPdf->descripcion }}</p>
            @endif
            <div class="flex flex-wrap gap-4 mt-3 text-xs text-neutral-400">
                <span>📄 {{ $archivoPdf->nombre_original }}</span>
                <span>💾 {{ $archivoPdf->tamanioLegible() }}</span>
                <span>👤 {{ $archivoPdf->subidoPor->name }}</span>
                <span>📅 {{ $archivoPdf->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        {{-- Visor embebido --}}
        <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden"
             style="height: 80vh;">
            <iframe src="{{ Storage::url($archivoPdf->ruta) }}"
                    class="w-full h-full"
                    type="application/pdf"
                    title="{{ $archivoPdf->nombre }}">
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <p class="text-neutral-500 dark:text-neutral-400 mb-4">
                        Tu navegador no puede mostrar este PDF directamente.
                    </p>
                    <a href="{{ Storage::url($archivoPdf->ruta) }}"
                       class="text-pink-600 underline font-semibold">
                        Descárgalo aquí
                    </a>
                </div>
            </iframe>
        </div>

        {{-- Conjuntos relacionados --}}
        @if($archivoPdf->conjuntos->count())
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 px-6 py-5">
                <h2 class="font-bold text-neutral-800 dark:text-white mb-3">
                    Conjuntos que usan este PDF
                </h2>
                <div class="space-y-2">
                    @foreach($archivoPdf->conjuntos as $conjunto)
                        <a href="{{ route('admin.conjuntos.show', $conjunto) }}"
                           class="flex items-center justify-between p-3 rounded-xl border border-neutral-200 dark:border-neutral-700 hover:border-pink-300 dark:hover:border-pink-700 hover:bg-pink-50 dark:hover:bg-pink-900/10 transition-all group"
                           wire:navigate>
                            <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200 group-hover:text-pink-600 dark:group-hover:text-pink-400">
                                {{ $conjunto->nombre }}
                            </span>
                            <svg class="w-4 h-4 text-neutral-400 group-hover:text-pink-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>