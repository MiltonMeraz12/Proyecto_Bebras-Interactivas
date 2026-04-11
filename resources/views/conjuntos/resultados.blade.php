<x-layouts.app :title="'Resultados — ' . $conjunto->nombre">
    <div class="flex flex-col gap-4 sm:gap-6 p-3 sm:p-4 lg:p-6 max-w-3xl mx-auto">

        {{-- Breadcrumb --}}
        <a href="{{ route('conjuntos.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-pink-600 hover:text-pink-700 dark:text-pink-400 self-start"
           wire:navigate>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a conjuntos
        </a>

        {{-- Puntuación --}}
        @php
            $total     = $preguntas->count();
            $correctas = $sesion->puntuacion ?? 0;
            $pct       = $total > 0 ? round(($correctas / $total) * 100) : 0;
            $emoji     = $pct >= 80 ? '🏆' : ($pct >= 50 ? '👍' : '💪');
        @endphp

        <div class="bg-white/95 dark:bg-neutral-900/90 border border-yellow-300/70 dark:border-neutral-700 rounded-3xl shadow-xl px-6 py-8 text-center">
            <div class="text-6xl mb-3">{{ $emoji }}</div>
            <h1 class="text-xl font-bold text-neutral-700 dark:text-neutral-300 mb-1">{{ $conjunto->nombre }}</h1>
            <p class="text-6xl font-extrabold bg-gradient-to-r from-yellow-500 via-pink-500 to-purple-500 bg-clip-text text-transparent mt-3">
                {{ $correctas }}/{{ $total }}
            </p>
            <p class="text-neutral-500 dark:text-neutral-400 mt-1 font-medium">preguntas correctas · {{ $pct }}% de éxito</p>

            @if($sesion->terminado_en)
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-3">
                    Completado el {{ $sesion->terminado_en->format('d/m/Y \a \l\a\s H:i') }}
                </p>
            @endif

            {{-- Barra de progreso --}}
            <div class="mt-5 w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 h-3 rounded-full transition-all"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- Detalle por pregunta --}}
        <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
                <h2 class="font-bold text-neutral-800 dark:text-white">Detalle de respuestas</h2>
            </div>
            <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @foreach($preguntas as $pregunta)
                    @php $prog = $progresos[$pregunta->id] ?? null; @endphp
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-xs font-bold
                                {{ $prog?->es_correcta ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                                    : ($prog ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                                    : 'bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400') }}">
                                {{ $pregunta->orden }}
                            </span>
                            <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate">
                                {{ $pregunta->titulo }}
                            </span>
                        </div>
                        @if($prog)
                            <span class="flex-shrink-0 ml-4 flex items-center gap-1 text-sm font-semibold
                                {{ $prog->es_correcta ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                @if($prog->es_correcta)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Correcta
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Incorrecta
                                @endif
                            </span>
                        @else
                            <span class="flex-shrink-0 ml-4 text-sm text-neutral-400">Sin respuesta</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('conjuntos.index') }}"
           class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white font-semibold px-6 py-3 rounded-2xl transition-all shadow-lg hover:shadow-xl"
           wire:navigate>
            Volver a conjuntos
        </a>
    </div>
</x-layouts.app>