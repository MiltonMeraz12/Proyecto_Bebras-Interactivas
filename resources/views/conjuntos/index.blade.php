<x-layouts.app :title="__('Conjuntos')">
    <div class="flex flex-col gap-4 sm:gap-6 p-3 sm:p-4 lg:p-6">

        {{-- Header de bienvenida --}}
        <div class="bg-white/95 dark:bg-neutral-900/90 border border-yellow-300/70 dark:border-neutral-700 rounded-3xl shadow-xl px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                ¡Hola, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-300 font-medium mt-1">
                Aquí puedes explorar los conjuntos de preguntas disponibles, ver tu progreso y resultados, y seguir aprendiendo. ¡Diviértete resolviendo los desafíos! 🚀
            </p>
        </div>

        {{-- Métricas personales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach([
                ['label' => 'Conjuntos',    'valor' => $totalConjuntos,   'color' => 'blue',   'emoji' => '📚'],
                ['label' => 'Completados',  'valor' => $completados,      'color' => 'green',  'emoji' => '✅'],
                ['label' => 'Respondidas',  'valor' => $totalRespondidas, 'color' => 'purple', 'emoji' => '💬'],
                ['label' => 'Correctas',    'valor' => $totalCorrectas,   'color' => 'yellow', 'emoji' => '⭐'],
            ] as $stat)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow p-4 text-center">
                    <div class="text-2xl mb-1">{{ $stat['emoji'] }}</div>
                    <div class="text-2xl font-extrabold text-neutral-900 dark:text-white">{{ $stat['valor'] }}</div>
                    <div class="text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Lista de conjuntos --}}
        @forelse($conjuntos as $conjunto)
            @php
                $sesionVal  = $sesiones->get($conjunto->id);
                $terminado  = $sesionVal !== null;
                $enProgreso = $sesiones->has($conjunto->id) && !$terminado;

                // Progreso dentro del conjunto
                $idPreguntas   = $conjunto->preguntas->pluck('id');
                $respConjunto  = \App\Models\ProgresoUsuario::where('user_id', auth()->id())
                    ->whereIn('pregunta_id', $idPreguntas)->count();
                $pctConjunto   = $conjunto->preguntas_count > 0
                    ? round(($respConjunto / $conjunto->preguntas_count) * 100)
                    : 0;
            @endphp

            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 p-5 sm:p-6 transition-all hover:shadow-2xl">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <h2 class="text-lg font-bold text-neutral-900 dark:text-white truncate">
                                {{ $conjunto->nombre }}
                            </h2>
                            @if($terminado)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">✓ Completado</span>
                            @elseif($enProgreso)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">⏳ En progreso</span>
                            @endif
                        </div>

                        @if($conjunto->descripcion)
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-3 line-clamp-2">
                                {{ $conjunto->descripcion }}
                            </p>
                        @endif

                        {{-- Barra de progreso del conjunto --}}
                        @if($enProgreso || $terminado)
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 mb-1">
                                    <span>{{ $respConjunto }}/{{ $conjunto->preguntas_count }} respondidas</span>
                                    <span>{{ $pctConjunto }}%</span>
                                </div>
                                <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 h-1.5 rounded-full transition-all"
                                         style="width: {{ $pctConjunto }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center gap-4 text-sm text-neutral-500 dark:text-neutral-400 flex-wrap">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $conjunto->preguntas_count }} pregunta(s)
                            </span>
                            @if($conjunto->pdf)
                                <a href="{{ Storage::url($conjunto->pdf->ruta) }}"
                                   target="_blank"
                                   class="flex items-center gap-1 text-pink-600 dark:text-pink-400 hover:underline font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    PDF disponible
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        @if($terminado)
                            <a href="{{ route('conjuntos.resultados', $conjunto) }}"
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-2xl transition-colors shadow-lg"
                               wire:navigate>
                                Ver resultados
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @elseif($enProgreso)
                            <a href="{{ route('conjuntos.show', $conjunto) }}"
                               class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-5 py-2.5 rounded-2xl transition-colors shadow-lg"
                               wire:navigate>
                                Continuar
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('conjuntos.show', $conjunto) }}"
                               class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white text-sm font-semibold px-5 py-2.5 rounded-2xl transition-all shadow-lg hover:shadow-xl"
                               wire:navigate>
                                Ver conjunto
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 p-12 text-center">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-neutral-500 dark:text-neutral-400 font-medium">No hay conjuntos disponibles por el momento.</p>
                <p class="text-xs text-neutral-400 mt-2">El administrador puede crear conjuntos desde el panel de administración.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>