<x-layouts.app :title="$pregunta->titulo">
    <div class="flex flex-col gap-4 p-3 sm:p-4 lg:p-6 max-w-4xl mx-auto">

        {{-- Barra de progreso del conjunto --}}
        @php
            $total       = $preguntas->count();
            $actual      = $posicion + 1;
            $pctBarra    = $total > 0 ? round(($actual / $total) * 100) : 0;
            // Cuántas preguntas de este conjunto ya tiene respuesta el alumno
            $respondidas = \App\Models\ProgresoUsuario::where('user_id', auth()->id())
                ->whereIn('pregunta_id', $preguntas->pluck('id'))
                ->count();
        @endphp

        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow px-5 py-3">
            <div class="flex items-center justify-between text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-2">
                <a href="{{ route('conjuntos.show', $conjunto) }}"
                   class="flex items-center gap-1 text-pink-600 dark:text-pink-400 hover:underline font-semibold"
                   wire:navigate>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ Str::limit($conjunto->nombre, 30) }}
                </a>
                <div class="flex items-center gap-3">
                    <span class="text-neutral-400">{{ $respondidas }}/{{ $total }} respondidas</span>
                    <span class="font-semibold">{{ $actual }} de {{ $total }}</span>
                </div>
            </div>
            {{-- Barra de posición actual --}}
            <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 h-1.5 rounded-full transition-all duration-500"
                     style="width: {{ $pctBarra }}%"></div>
            </div>
            {{-- Miniaturas de navegación rápida (solo en desktop) --}}
            <div class="hidden md:flex gap-1 mt-2 overflow-x-auto pb-1">
                @foreach($preguntas as $i => $p)
                    @php
                        $prog = \App\Models\ProgresoUsuario::where('user_id', auth()->id())
                            ->where('pregunta_id', $p)->first();
                        $esActual = $p === $pregunta->id;
                    @endphp
                    <a href="{{ route('preguntas.show', [$conjunto, $p]) }}"
                       wire:navigate
                       class="flex-shrink-0 w-7 h-7 rounded-lg text-xs font-bold flex items-center justify-center transition-all
                           {{ $esActual
                               ? 'bg-pink-500 text-white shadow-md scale-110'
                               : ($prog
                                   ? ($prog->es_correcta
                                       ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                       : 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400')
                                   : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500 hover:bg-neutral-200') }}"
                       title="Pregunta {{ $i + 1 }}">
                        {{ $i + 1 }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Tarjeta de la pregunta --}}
        <div class="bg-white/95 dark:bg-neutral-900/90 border border-yellow-300/70 dark:border-neutral-700 rounded-3xl shadow-xl overflow-hidden">

            {{-- Header --}}
            <div class="px-6 pt-6 pb-4 border-b border-neutral-100 dark:border-neutral-800">
                <div class="flex items-center gap-3 flex-wrap mb-2">
                    @if($pregunta->nivel)
                        <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold">
                            Nivel {{ $pregunta->nivel }}
                        </span>
                    @endif
                    @if($pregunta->dificultad)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $pregunta->dificultad === 'Baja'  ? 'bg-green-50  dark:bg-green-900/30  text-green-700  dark:text-green-300'  : '' }}
                            {{ $pregunta->dificultad === 'Media' ? 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' : '' }}
                            {{ $pregunta->dificultad === 'Alta'  ? 'bg-red-50    dark:bg-red-900/30    text-red-700    dark:text-red-300'    : '' }}">
                            {{ $pregunta->dificultad }}
                        </span>
                    @endif
                    @if($pregunta->pais_origen)
                        <span class="text-xs text-neutral-400 dark:text-neutral-500">{{ $pregunta->pais_origen }}</span>
                    @endif
                    @if($pregunta->codigo_tarea)
                        <span class="text-xs text-neutral-300 dark:text-neutral-600 font-mono">{{ $pregunta->codigo_tarea }}</span>
                    @endif
                </div>
                <h1 class="text-xl md:text-2xl font-extrabold text-neutral-900 dark:text-white">
                    {{ $pregunta->titulo }}
                </h1>
            </div>

            {{-- Enunciado --}}
            <div class="px-6 py-5">
                <div class="prose prose-sm dark:prose-invert max-w-none text-neutral-700 dark:text-neutral-300 leading-relaxed">
                    {!! nl2br(e($pregunta->enunciado)) !!}
                </div>
                @if($pregunta->imagen_enunciado)
                    <div class="mt-4 rounded-2xl overflow-hidden border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800">
                        <img src="{{ asset('storage/' . $pregunta->imagen_enunciado) }}"
                             alt="Imagen del enunciado"
                             class="w-full object-contain max-h-80 mx-auto">
                    </div>
                @endif
            </div>

            {{-- Área de interacción --}}
            <div class="px-6 pb-6" id="area-interaccion">
                @if(!empty($pregunta->tipo_interaccion))
                    @include('preguntas.tipos.' . $pregunta->tipo_interaccion, [
                        'config'          => $pregunta->configuracion,
                        'progreso'        => $progreso,
                        'progresoUsuario' => $progreso,
                    ])
                @else
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4 text-yellow-700 dark:text-yellow-300 text-sm">
                        ⚠️ Esta pregunta aún no tiene un tipo de interacción implementado.
                    </div>
                @endif
            </div>

            {{-- Botón verificar --}}
            @if(!$progreso)
                <div class="px-6 pb-6">
                    <button id="btn-verificar"
                            onclick="verificarRespuesta()"
                            class="w-full bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white font-bold py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 text-base cursor-pointer">
                        Verificar respuesta
                    </button>
                </div>
            @endif

            {{-- Panel de resultado --}}
            <div id="panel-resultado" class="{{ $progreso ? '' : 'hidden' }} px-6 pb-6">
                <div id="resultado-contenido"
                     class="rounded-2xl p-5 border
                        {{ $progreso?->es_correcta
                            ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'
                            : ($progreso ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700') }}">

                    @if($progreso)
                        <div class="flex items-center gap-3 mb-3">
                            @if($progreso->es_correcta)
                                <span class="text-2xl">✅</span>
                                <span class="font-bold text-green-700 dark:text-green-300 text-lg">¡Correcto!</span>
                            @else
                                <span class="text-2xl">❌</span>
                                <span class="font-bold text-red-700 dark:text-red-300 text-lg">Incorrecto</span>
                            @endif
                        </div>
                        @if($pregunta->explicacion)
                            <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed">{{ $pregunta->explicacion }}</p>
                        @endif
                        @if($pregunta->imagen_explicacion)
                            <img src="{{ asset('storage/' . $pregunta->imagen_explicacion) }}"
                                 alt="Explicación"
                                 class="mt-3 rounded-xl max-h-64 object-contain w-full">
                        @endif
                    @else
                        <div class="flex items-center gap-3 mb-3">
                            <span id="resultado-emoji" class="text-2xl"></span>
                            <span id="resultado-titulo" class="font-bold text-lg"></span>
                        </div>
                        <p id="resultado-respuesta" class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-2"></p>
                        <p id="resultado-explicacion" class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed"></p>
                        <img id="resultado-imagen" src="" alt="Explicación"
                             class="hidden mt-3 rounded-xl max-h-64 object-contain w-full">
                    @endif
                </div>
            </div>
        </div>

        {{-- Navegación --}}
        <div class="flex items-center justify-between gap-3">
            @if($anterior)
                <a href="{{ route('preguntas.show', [$conjunto, $anterior]) }}"
                   class="flex items-center gap-2 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 text-neutral-700 dark:text-neutral-300 font-semibold px-5 py-2.5 rounded-2xl shadow hover:shadow-md transition-all text-sm"
                   wire:navigate>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Anterior
                </a>
            @else
                <div></div>
            @endif

            @if($siguiente)
                <a href="{{ route('preguntas.show', [$conjunto, $siguiente]) }}"
                   class="flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 text-white font-semibold px-5 py-2.5 rounded-2xl shadow-lg hover:shadow-xl transition-all text-sm"
                   wire:navigate>
                    Siguiente
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                @if(!$sesion?->estaTerminada())
                    <form method="POST" action="{{ route('conjuntos.finalizar', $conjunto) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-2xl shadow-lg transition-colors text-sm cursor-pointer">
                            Finalizar conjunto
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('conjuntos.resultados', $conjunto) }}"
                       class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-2xl shadow-lg transition-colors text-sm"
                       wire:navigate>
                        Ver resultados
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif
            @endif
        </div>
    </div>

    {{-- SortableJS global (solo si el tipo lo necesita) --}}
    @if(in_array($pregunta->tipo_interaccion, ['ordenar', 'completar']))
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endif

    @if(!empty($pregunta->tipo_interaccion))
        @include('preguntas.scripts.' . $pregunta->tipo_interaccion)
    @endif

    <script>
        window._bebrasResp = {{ $progreso ? 'true' : 'false' }};

        window.verificarRespuesta = async function() {
            if (window._bebrasResp) return;

            const VERIFICAR_URL = "{{ route('preguntas.verificar', [$conjunto, $pregunta]) }}";
            const CSRF_TOKEN    = "{{ csrf_token() }}";
            const respuesta     = typeof obtenerRespuesta === 'function' ? obtenerRespuesta() : null;

            if (respuesta === null || respuesta === undefined) {
                // Intentar mensaje específico del tipo antes del alert genérico
                const msg = typeof getMensajeIncompleto === 'function'
                    ? getMensajeIncompleto()
                    : 'Selecciona una respuesta antes de continuar.';
                alert(msg);
                return;
            }

            const btn = document.getElementById('btn-verificar');
            if (btn) { btn.disabled = true; btn.textContent = 'Verificando...'; }

            try {
                const res  = await fetch(VERIFICAR_URL, {
                    method : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN' : CSRF_TOKEN,
                        'Accept'       : 'application/json',
                    },
                    body: JSON.stringify({ respuesta }),
                });
                const data = await res.json();

                if (data.error) {
                    alert(data.mensaje ?? 'Ocurrió un error.');
                    if (btn) { btn.disabled = false; btn.textContent = 'Verificar respuesta'; }
                    return;
                }

                window._bebrasResp = true;
                if (typeof deshabilitarInteraccion === 'function') deshabilitarInteraccion();
                if (btn) btn.classList.add('hidden');

                const panel    = document.getElementById('panel-resultado');
                const emoji    = document.getElementById('resultado-emoji');
                const titEl    = document.getElementById('resultado-titulo');
                const respVis  = document.getElementById('resultado-respuesta');
                const explicEl = document.getElementById('resultado-explicacion');
                const imgEl    = document.getElementById('resultado-imagen');
                const cont     = document.getElementById('resultado-contenido');

                panel.classList.remove('hidden');

                if (data.correcta) {
                    cont.className  = 'rounded-2xl p-5 border bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800';
                    emoji.textContent = '✅';
                    titEl.textContent = '¡Correcto!';
                    titEl.className   = 'font-bold text-green-700 dark:text-green-300 text-lg';
                } else {
                    cont.className  = 'rounded-2xl p-5 border bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800';
                    emoji.textContent = '❌';
                    titEl.textContent = 'Incorrecto';
                    titEl.className   = 'font-bold text-red-700 dark:text-red-300 text-lg';
                }

                if (respVis  && data.respuesta_correcta_visual) respVis.textContent  = data.respuesta_correcta_visual;
                if (explicEl && data.explicacion)               explicEl.textContent = data.explicacion;
                if (imgEl    && data.imagen_explicacion) {
                    imgEl.src = '/storage/' + data.imagen_explicacion;
                    imgEl.classList.remove('hidden');
                }

            } catch (e) {
                alert('Error de conexión. Intenta de nuevo.');
                if (btn) { btn.disabled = false; btn.textContent = 'Verificar respuesta'; }
            }
        };
    </script>
</x-layouts.app>