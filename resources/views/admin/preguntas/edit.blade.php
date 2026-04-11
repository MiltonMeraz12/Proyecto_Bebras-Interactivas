<x-layouts.app :title="'Editar — ' . $pregunta->titulo">
    <div class="flex flex-col gap-6 p-4 lg:p-6 max-w-5xl mx-auto w-full">

        <a href="{{ route('admin.conjuntos.show', $conjunto) }}"
           class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors w-fit"
           wire:navigate>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Conjunto
        </a>

        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 px-6 py-6 md:px-8 md:py-8">
            <div class="border-b border-neutral-200 dark:border-neutral-800 pb-5 mb-6 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                        Editar Pregunta
                    </h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                        Orden <span class="font-semibold text-pink-500">#{{ $pregunta->orden }}</span>
                        en <span class="font-semibold text-white">"{{ $conjunto->nombre }}"</span>
                    </p>
                </div>
                {{-- Toggle activa --}}
                <form method="POST"
                      action="{{ route('admin.preguntas.toggle', [$conjunto, $pregunta]) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors
                                {{ $pregunta->activa
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 hover:bg-green-200'
                                    : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400 hover:bg-neutral-200' }}">
                        {{ $pregunta->activa ? '✓ Activa' : '○ Inactiva' }}
                    </button>
                </form>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-2xl px-5 py-4 text-sm">
                    <p class="font-semibold mb-1">Corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.preguntas.update', [$conjunto, $pregunta]) }}"
                  method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Fila 1: Metadatos --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                            Título *
                        </label>
                        <input type="text" name="titulo" value="{{ old('titulo', $pregunta->titulo) }}" required
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                            Tipo de Interacción *
                        </label>
                        <select name="tipo_interaccion" required
                                class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo }}"
                                    {{ old('tipo_interaccion', $pregunta->tipo_interaccion) === $tipo ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', Str::title($tipo)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Nivel</label>
                            <select name="nivel"
                                    class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                                @foreach(['I','II','III','IV','V','VI'] as $n)
                                    <option value="{{ $n }}"
                                        {{ old('nivel', $pregunta->nivel) === $n ? 'selected' : '' }}>{{ $n }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Dificultad</label>
                            <select name="dificultad"
                                    class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                                @foreach(['Baja','Media','Alta'] as $d)
                                    <option value="{{ $d }}"
                                        {{ old('dificultad', $pregunta->dificultad) === $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Orden</label>
                            <input type="number" name="orden" value="{{ old('orden', $pregunta->orden) }}" min="1" required
                                   class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">País de Origen</label>
                        <input type="text" name="pais_origen" value="{{ old('pais_origen', $pregunta->pais_origen) }}"
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Código de Tarea</label>
                        <input type="text" name="codigo_tarea" value="{{ old('codigo_tarea', $pregunta->codigo_tarea) }}"
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none"
                               placeholder="Ej: 2022-DE-06">
                    </div>
                </div>

                <hr class="border-neutral-200 dark:border-neutral-800">

                {{-- Enunciado --}}
                <div>
                    <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                        Enunciado *
                    </label>
                    <textarea name="enunciado" rows="5" required
                              class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">{{ old('enunciado', $pregunta->enunciado) }}</textarea>
                </div>

                <hr class="border-neutral-200 dark:border-neutral-800">

                {{-- Configuración y Respuesta --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                            Configuración (JSON)
                        </label>
                        <textarea name="configuracion" rows="10"
                                  class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white font-mono focus:border-pink-500 outline-none">{{ old('configuracion', $pregunta->configuracion ? json_encode($pregunta->configuracion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                            Respuesta Correcta (JSON) *
                        </label>
                        <textarea name="respuesta_correcta" rows="10" required
                                  class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white font-mono focus:border-pink-500 outline-none">{{ old('respuesta_correcta', $pregunta->respuesta_correcta ? json_encode($pregunta->respuesta_correcta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                        <p class="text-xs text-neutral-400 mt-1">Tipo actual: <code class="bg-neutral-100 dark:bg-neutral-800 px-1 rounded">{{ $pregunta->tipo_interaccion }}</code></p>
                    </div>
                </div>

                <hr class="border-neutral-200 dark:border-neutral-800">

                {{-- Explicación --}}
                <div>
                    <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                        Explicación
                    </label>
                    <textarea name="explicacion" rows="3"
                              class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">{{ old('explicacion', $pregunta->explicacion) }}</textarea>
                </div>

                {{-- Acciones --}}
                <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-6 border-t border-neutral-200 dark:border-neutral-800">
                    <button type="button"
                            onclick="if(confirm('¿Eliminar esta pregunta? Esta acción no se puede deshacer.')) document.getElementById('delete-form').submit();"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-red-500 hover:text-red-600 px-3 py-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors w-full sm:w-auto justify-center sm:justify-start">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar Pregunta
                    </button>

                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.conjuntos.show', $conjunto) }}"
                           class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-xl border border-neutral-300 dark:border-neutral-700 px-5 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                           wire:navigate>
                            Cancelar
                        </a>
                        <button type="submit"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-form"
                  action="{{ route('admin.preguntas.destroy', [$conjunto, $pregunta]) }}"
                  method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <div class="flex items-center justify-between mb-2">
        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
            Configuración (JSON)
        </label>
        <button type="button" onclick="cargarPlantillaEdit()"
                class="text-xs text-pink-600 dark:text-pink-400 hover:underline font-semibold">
            ↺ Ver plantilla del tipo
        </button>
    </div>

    <script>
        function cargarPlantillaEdit() {
            const tipo  = document.querySelector('[name="tipo_interaccion"]').value;
            const datos = PLANTILLAS[tipo];
            if (!datos) { alert('No hay plantilla para este tipo.'); return; }

            if (confirm('¿Ver la plantilla para "' + tipo + '"? Se mostrará en una ventana separada y no modificará el formulario.')) {
                const win = window.open('', '_blank', 'width=600,height=400');
                win.document.write('<pre style="font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4;min-height:100vh">' +
                    '<b>Configuración:</b>\n' + JSON.stringify(datos.config, null, 2) +
                    '\n\n<b>Respuesta de ejemplo:</b>\n' + JSON.stringify(datos.respuesta, null, 2) +
                    '\n\n<b>Guía:</b>\n' + datos.guia.replace(/<[^>]*>/g, '') +
                    '</pre>');
            }
        }
    </script>
</x-layouts.app>