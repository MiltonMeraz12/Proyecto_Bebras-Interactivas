<x-layouts.app :title="'Progreso de ' . $usuario->name">
    <div class="flex flex-col gap-6 p-4 lg:p-6">
        <a href="{{ route('admin.dashboard') }}"
            class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Panel
        </a>

        {{-- Header --}}
        <div class="bg-white/95 dark:bg-neutral-900/90 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-xl px-6 py-5">
            <h1 class="text-2xl font-extrabold text-neutral-900 dark:text-white">
                Progreso de {{ $usuario->name }}
            </h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $usuario->email }}</p>
        </div>

        {{-- Sesiones por conjunto --}}
        @forelse($sesiones as $sesion)
            @php
                $conjunto      = $sesion->conjunto;
                $preguntasConj = $conjunto->preguntas;
                $idsPreguntas  = $preguntasConj->pluck('id');
                $progresosConj = $progresos->only($idsPreguntas->toArray());
                $correctasConj = $progresosConj->filter(fn($p) => $p->es_correcta)->count();
                $total         = $preguntasConj->count();
            @endphp

            {{-- Tarjeta del Conjunto (Le agregamos x-data para controlar si está abierto o cerrado) --}}
            <div x-data="{ abierto: false }" class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden transition-all duration-300">
                
                {{-- Header del conjunto (Ahora es clickeable) --}}
                <div @click="abierto = !abierto" 
                     class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between flex-wrap gap-3 cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <div>
                        <h2 class="font-bold text-neutral-800 dark:text-white">{{ $conjunto->nombre }}</h2>
                        <p class="text-xs text-neutral-400 mt-0.5">
                            Iniciado: {{ $sesion->iniciado_en->format('d/m/Y H:i') }}
                            @if($sesion->terminado_en)
                                · Completado: {{ $sesion->terminado_en->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        {{-- Badges y Puntuación --}}
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-extrabold bg-gradient-to-r from-yellow-500 via-pink-500 to-purple-500 bg-clip-text text-transparent">
                                {{ $correctasConj }}/{{ $total }}
                            </span>
                            @if($sesion->estaTerminada())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                    Completado
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                                    En progreso
                                </span>
                            @endif
                        </div>

                        {{-- Flecha indicadora (gira cuando se abre) --}}
                        <div class="text-neutral-400 dark:text-neutral-500">
                            <svg class="w-5 h-5 transition-transform duration-300" :class="abierto ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Preguntas del conjunto (Se muestran/ocultan con animación) --}}
                <div x-show="abierto" x-transition style="display: none;" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach($preguntasConj->sortBy('orden') as $pregunta)
                        @php $prog = $progresos[$pregunta->id] ?? null; @endphp
                        <div class="flex items-center justify-between px-6 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold
                                    {{ $prog?->es_correcta ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                                        : ($prog ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                                        : 'bg-neutral-100 text-neutral-500 dark:bg-neutral-700') }}">
                                    {{ $pregunta->orden }}
                                </span>
                                <span class="text-sm text-neutral-800 dark:text-neutral-200 truncate">{{ $pregunta->titulo }}</span>
                            </div>
                            @if($prog)
                                <span class="flex-shrink-0 ml-4 text-xs font-semibold
                                    {{ $prog->es_correcta ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                    {{ $prog->es_correcta ? '✓ Correcta' : '✗ Incorrecta' }}
                                </span>
                            @else
                                <span class="flex-shrink-0 ml-4 text-xs text-neutral-400">Sin responder</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 p-12 text-center">
                <div class="text-5xl mb-3">📭</div>
                <p class="text-neutral-500 dark:text-neutral-400">Este alumno aún no ha iniciado ningún conjunto.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>