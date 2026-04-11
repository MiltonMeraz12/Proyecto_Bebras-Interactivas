<x-layouts.app :title="$conjunto->nombre">
    <div class="flex flex-col gap-4 sm:gap-6 p-3 sm:p-4 lg:p-6 max-w-3xl mx-auto">

        {{-- Breadcrumb --}}
        <a href="{{ route('conjuntos.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-pink-600 hover:text-pink-700 dark:text-pink-400 dark:hover:text-pink-300 self-start"
           wire:navigate>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a conjuntos
        </a>

        {{-- Información del conjunto --}}
        <div class="bg-white/95 dark:bg-neutral-900/90 border border-yellow-300/70 dark:border-neutral-700 rounded-3xl shadow-xl px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                {{ $conjunto->nombre }}
            </h1>
            @if($conjunto->descripcion)
                <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ $conjunto->descripcion }}</p>
            @endif

            <div class="mt-4 flex items-center gap-5 text-sm text-neutral-500 dark:text-neutral-400 flex-wrap">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <strong class="text-neutral-700 dark:text-neutral-200">{{ $conjunto->totalPreguntas() }}</strong> preguntas activas
                </span>

                @if($conjunto->pdf)
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-2xl p-5 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center text-2xl shadow-sm">
                                📄
                            </div>
                            <div>
                                <h4 class="font-bold text-neutral-900 dark:text-white text-sm">Material de Apoyo (PDF)</h4>
                                <p class="text-xs text-neutral-500">Descarga los problemas para resolver en papel.</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $conjunto->pdf->ruta) }}" 
                        target="_blank"
                        class="px-4 py-2 bg-neutral-800 dark:bg-white text-white dark:text-neutral-900 text-xs font-bold rounded-lg hover:scale-105 transition-transform shadow-md">
                            Descargar
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Estado y acción --}}
        <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 p-6">
            @if(($sesion ?? null)?->estaTerminada())
                <div class="text-center py-4">
                    <div class="text-5xl mb-3">🏆</div>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400 mb-1">¡Conjunto completado!</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-5">
                        Obtuviste {{ $sesion->puntuacion }} de {{ $conjunto->totalPreguntas() }} respuestas correctas.
                    </p>
                    <a href="{{ route('conjuntos.resultados', $conjunto) }}"
                       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-2xl transition-colors shadow-lg"
                       wire:navigate>
                        Ver mis resultados detallados
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

            @elseif($sesion)
                {{-- En progreso: buscar primera sin responder --}}
                @php
                    $primerasinResponder = $conjunto->preguntas
                        ->whereNotIn('id', $preguntasRespondidas->toArray())
                        ->first() ?? $conjunto->preguntas->first();
                @endphp
                <div class="text-center py-4">
                    <div class="text-5xl mb-3">⏳</div>
                    <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mb-1">En progreso</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-5">
                        Tienes respuestas guardadas. Puedes continuar donde lo dejaste.
                    </p>
                    @if($primerasinResponder)
                        <a href="{{ route('preguntas.show', [$conjunto, $primerasinResponder]) }}"
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white font-semibold px-6 py-3 rounded-2xl transition-all shadow-lg hover:shadow-xl"
                           wire:navigate>
                            Continuar
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                </div>

            @else
                <div class="text-center py-4">
                    <div class="text-5xl mb-3">🧩</div>
                    <p class="text-lg font-bold text-neutral-800 dark:text-white mb-1">¿Listo para comenzar?</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-5">
                        Una vez iniciado, tus respuestas quedan guardadas. Puedes pausar y continuar cuando quieras.
                    </p>
                    <form method="POST" action="{{ route('conjuntos.iniciar', $conjunto) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white font-semibold px-8 py-3 rounded-2xl transition-all shadow-lg hover:shadow-xl cursor-pointer">
                            🚀 Iniciar conjunto
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>