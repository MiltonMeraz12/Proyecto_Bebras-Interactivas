<x-layouts.app :title="'Agregar Pregunta a ' . $conjunto->nombre">
    <div class="flex flex-col gap-6 p-4 lg:p-6 max-w-5xl mx-auto w-full">

        <a href="{{ route('admin.conjuntos.show', $conjunto) }}"
           class="text-pink-600 dark:text-pink-400 hover:text-pink-700 font-semibold text-xs flex items-center gap-1 transition-colors w-fit"
           wire:navigate>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Conjunto
        </a>

        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 px-6 py-6 md:px-8 md:py-8">
            <div class="border-b border-neutral-200 dark:border-neutral-800 pb-5 mb-6">
                <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                    Agregar Nueva Pregunta
                </h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                    Conjunto: <span class="font-semibold text-pink-500">"{{ $conjunto->nombre }}"</span>
                </p>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-2xl px-5 py-4 text-sm">
                    <p class="font-semibold mb-1">Corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.preguntas.store', $conjunto) }}" method="POST" class="space-y-6">
                @csrf

                {{-- Metadatos básicos --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Título *</label>
                        <input type="text" name="titulo" value="{{ old('titulo') }}" required
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none transition-all"
                               placeholder="Ej: Libros Populares">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Tipo de Interacción *</label>
                        <select name="tipo_interaccion" id="tipo_interaccion" required
                                onchange="actualizarPlantilla()"
                                class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo_interaccion') === $tipo ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', Str::title($tipo)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Nivel</label>
                            <select name="nivel" class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                                @foreach(['I','II','III','IV','V','VI'] as $n)
                                    <option value="{{ $n }}" {{ old('nivel') === $n ? 'selected' : '' }}>{{ $n }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Dificultad</label>
                            <select name="dificultad" class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                                @foreach(['Baja','Media','Alta'] as $d)
                                    <option value="{{ $d }}" {{ old('dificultad') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Orden</label>
                            <input type="number" name="orden" value="{{ old('orden', $siguienteOrden) }}" min="1" required
                                   class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">País de Origen</label>
                        <input type="text" name="pais_origen" value="{{ old('pais_origen') }}"
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none"
                               placeholder="Ej: Alemania">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Código de Tarea</label>
                        <input type="text" name="codigo_tarea" value="{{ old('codigo_tarea') }}"
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none"
                               placeholder="Ej: 2022-DE-06">
                    </div>
                </div>

                <hr class="border-neutral-200 dark:border-neutral-800">

                {{-- Enunciado --}}
                <div>
                    <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Enunciado *</label>
                    <textarea name="enunciado" rows="5" required
                              class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none"
                              placeholder="Describe el problema completo aquí...">{{ old('enunciado') }}</textarea>
                </div>

                <hr class="border-neutral-200 dark:border-neutral-800">

                {{-- Guía dinámica por tipo --}}
                <div id="guia-tipo" class="bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-2xl p-4 flex items-start gap-3 transition-all duration-300">
                    
                    {{-- Ícono de información (Acento rosa) --}}
                    <div class="flex-shrink-0 text-pink-500 dark:text-pink-400 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    {{-- Contenido de la guía --}}
                    <div>
                        <h4 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 mb-1" id="guia-titulo">
                            Guía de Configuración
                        </h4>
                        <div class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed space-y-2" id="guia-texto">
                            Selecciona un <strong>Tipo de Interacción</strong> en la parte superior para ver cómo debes estructurar el formato JSON.
                        </div>
                    </div>
                </div>

                {{-- Configuración y Respuesta --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                Configuración (JSON)
                            </label>
                            <button type="button" onclick="cargarPlantilla()"
                                    class="text-xs text-pink-600 dark:text-pink-400 hover:underline font-semibold">
                                ↺ Cargar plantilla del tipo
                            </button>
                        </div>
                        <textarea name="configuracion" id="campo-configuracion" rows="12"
                                  class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white font-mono focus:border-pink-500 outline-none"
                                  placeholder="{}">{{ old('configuracion') }}</textarea>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                Respuesta Correcta (JSON) *
                            </label>
                            <span id="hint-respuesta" class="text-xs text-neutral-400 font-mono"></span>
                        </div>
                        <textarea name="respuesta_correcta" id="campo-respuesta" rows="12" required
                                  class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white font-mono focus:border-pink-500 outline-none"
                                  placeholder='["B"]'>{{ old('respuesta_correcta') }}</textarea>
                    </div>
                </div>

                <hr class="border-neutral-200 dark:border-neutral-800">

                <div>
                    <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Explicación</label>
                    <textarea name="explicacion" rows="3"
                              class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none"
                              placeholder="Explica por qué la respuesta es correcta...">{{ old('explicacion') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                    <a href="{{ route('admin.conjuntos.show', $conjunto) }}"
                       class="inline-flex items-center justify-center rounded-xl border border-neutral-300 dark:border-neutral-700 px-5 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                        Guardar Pregunta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Plantillas JSON y guías por tipo
        const PLANTILLAS = {
            seleccion_simple: {
                config: {
                    "opciones": [
                        {"id": "A", "tipo": "texto", "valor": "Primera opción"},
                        {"id": "B", "tipo": "texto", "valor": "Segunda opción"},
                        {"id": "C", "tipo": "texto", "valor": "Tercera opción"},
                        {"id": "D", "tipo": "texto", "valor": "Cuarta opción"}
                    ]
                },
                respuesta: ["B"],
                guia: `<strong>Selección Simple</strong> — El alumno elige una opción.<br>
                    <code>tipo</code>: "texto" o "imagen" (si es imagen, <code>valor</code> es la ruta en storage).<br>
                    <strong>Respuesta:</strong> Array con el id correcto: <code>["B"]</code>`
            },
            seleccion_multiple: {
                config: {
                    "opciones": [
                        {"id": "A", "valor": "Primera opción"},
                        {"id": "B", "valor": "Segunda opción"},
                        {"id": "C", "valor": "Tercera opción"},
                        {"id": "D", "valor": "Cuarta opción"}
                    ],
                    "nota": "Puede haber más de una respuesta correcta."
                },
                respuesta: ["A", "C"],
                guia: `<strong>Selección Múltiple</strong> — El alumno marca varias opciones con checkboxes.<br>
                    <strong>Respuesta:</strong> Array con todos los ids correctos: <code>["A", "C"]</code>`
            },
            ordenar: {
                config: {
                    "elementos": [
                        {"id": "1", "nombre": "Primer paso"},
                        {"id": "2", "nombre": "Segundo paso"},
                        {"id": "3", "nombre": "Tercer paso"},
                        {"id": "4", "nombre": "Cuarto paso"}
                    ]
                },
                respuesta: [["1", "2", "3", "4"]],
                guia: `<strong>Ordenar</strong> — El alumno arrastra elementos al orden correcto (SortableJS).<br>
                    <strong>Respuesta:</strong> Array envuelto en otro array (permite múltiples soluciones): <code>[["1","2","3","4"]]</code><br>
                    Para aceptar más de un orden válido: <code>[["1","2","3","4"], ["4","3","2","1"]]</code>`
            },
            grid_seleccion: {
                config: {
                    "labels_filas": ["Fila 1", "Fila 2", "Fila 3"],
                    "labels_columnas": ["Col A", "Col B", "Col C"],
                    "estado_inicial": [
                        [false, false, false],
                        [false, true,  false],
                        [false, false, false]
                    ],
                    "numeros_celdas": [
                        [1, 2, 3],
                        [4, 5, 6],
                        [7, 8, 9]
                    ]
                },
                respuesta: [{"fila": 0, "columna": 1}, {"fila": 2, "columna": 2}],
                guia: `<strong>Grid de Selección</strong> — El alumno hace clic en celdas de una cuadrícula.<br>
                    <code>estado_inicial</code>: celdas pre-marcadas (true = marcada).<br>
                    <code>numeros_celdas</code>: número que aparece en cada celda.<br>
                    <strong>Respuesta:</strong> Array de objetos <code>[{"fila": 0, "columna": 1}]</code>`
            },
            emparejar: {
                config: {
                    "objetos": [
                        {"id": "obj1", "nombre": "Objeto 1", "imagen": ""},
                        {"id": "obj2", "nombre": "Objeto 2", "imagen": ""}
                    ],
                    "destinos": [
                        {"id": "dest1", "nombre": "Destino A"},
                        {"id": "dest2", "nombre": "Destino B"}
                    ]
                },
                respuesta: [{"objeto": "obj1", "destino": "dest1"}, {"objeto": "obj2", "destino": "dest2"}],
                guia: `<strong>Emparejar</strong> — El alumno relaciona objetos con destinos mediante selects.<br>
                    <strong>Respuesta:</strong> Array de pares <code>[{"objeto":"obj1","destino":"dest1"}]</code>`
            },
            rellenar: {
                config: {
                    "colores_disponibles": ["verde", "amarillo", "azul"],
                    "areas": [
                        {"id": "fondo", "nombre": "Fondo"},
                        {"id": "flor", "nombre": "Flor"},
                        {"id": "centro", "nombre": "Centro"}
                    ]
                },
                respuesta: [{"area": "fondo", "color": "amarillo"}, {"area": "flor", "color": "verde"}, {"area": "centro", "color": "amarillo"}],
                guia: `<strong>Rellenar</strong> — El alumno colorea áreas seleccionando colores.<br>
                    <strong>Respuesta:</strong> Array de objetos <code>[{"area":"fondo","color":"amarillo"}]</code>`
            },
            texto_libre: {
                config: {
                    "tipo_respuesta": "numero",
                    "min": 0,
                    "max": 999
                },
                respuesta: ["4"],
                guia: `<strong>Texto Libre</strong> — El alumno escribe una respuesta.<br>
                    <code>tipo_respuesta</code>: "numero" (input numérico) o cualquier otro (textarea).<br>
                    <strong>Respuesta:</strong> Array con el valor exacto: <code>["4"]</code> o <code>["respuesta correcta"]</code>`
            },
            completar: {
                config: {
                    "opciones": [
                        {"id": "A", "valor": "Opción A"},
                        {"id": "B", "valor": "Opción B"},
                        {"id": "C", "valor": "Opción C"}
                    ]
                },
                respuesta: ["A", "C"],
                guia: `<strong>Completar</strong> — Tipo flexible. Soporta checkboxes, slots, blanks y strings.<br>
                    Para checkboxes básicos: <code>opciones</code> con ids y valores.<br>
                    Para respuesta de texto: añade <code>"formato": "string"</code>.<br>
                    Para casillas: añade <code>"slots": {"lunes": "Lunes", "martes": "Martes"}</code>.<br>
                    <strong>Respuesta:</strong> Varía según subtipo. Para checkboxes: <code>["A","C"]</code>`
            },
            colocar_piezas: {
                config: {
                    "celdas_hexagonales": 7,
                    "abejas": [
                        {"id": "1", "imagen": "preguntas/15/abeja1.png"},
                        {"id": "2", "imagen": "preguntas/15/abeja2.png"}
                    ]
                },
                respuesta: [{"abeja": "1", "celda": 3}, {"abeja": "2", "celda": 5}],
                guia: `<strong>Colocar Piezas</strong> — Drag & drop de piezas en un panal hexagonal.<br>
                    <code>celdas_hexagonales</code>: 7 (panal pequeño) o 19 (panal completo).<br>
                    <strong>Respuesta:</strong> <code>[{"abeja":"1","celda":3}]</code>`
            },
            colorear_hexagonos: {
                config: {
                    "colores_disponibles": ["verde", "amarillo", "azul"],
                    "filas": 5,
                    "hexagonos_iniciales": [
                        {"posicion": [4, 0], "color": "verde"},
                        {"posicion": [4, 1], "color": "amarillo"},
                        {"posicion": [4, 2], "color": "azul"}
                    ]
                },
                respuesta: [{"posicion": [0, 0], "color": "verde"}],
                guia: `<strong>Colorear Hexágonos</strong> — Pirámide de hexágonos a colorear siguiendo reglas.<br>
                    <code>hexagonos_iniciales</code>: piezas fijas ya coloreadas (fila, columna desde 0).<br>
                    <code>filas</code>: cuántas filas tiene la pirámide.<br>
                    <strong>Respuesta:</strong> Solo las piezas que el alumno debe colorear: <code>[{"posicion":[0,0],"color":"verde"}]</code>`
            },
            tejer_alfombra: {
                config: {
                    "filas": 6,
                    "columnas": 6,
                    "simbolos_disponibles": ["purple", "red", "yellow", "green"]
                },
                respuesta: [["M","M","M","M","M","M"],["M","R","R","R","R","M"],["M","R","A","A","R","M"],["M","R","A","A","R","M"],["M","R","R","R","R","M"],["M","M","M","M","M","M"]],
                guia: `<strong>Tejer Alfombra</strong> — Grid NxN donde cada celda recibe un símbolo/color.<br>
                    Símbolos: purple=M, red=R, yellow=A, green=V.<br>
                    <strong>Respuesta:</strong> Array 2D con letras correspondientes a cada celda.`
            }
        };

        const selectTipo = document.querySelector('select[name="tipo_interaccion"]');
        const guiaTitulo = document.getElementById('guia-titulo');
        const guiaTexto = document.getElementById('guia-texto');
        const campoConfig = document.getElementById('campo-configuracion');
        const campoRespuesta = document.getElementById('campo-respuesta');

        function actualizarPlantilla() {
            const tipo = selectTipo.value;
            const datos = PLANTILLAS[tipo];
            
            if (datos) {
                // Formatea el nombre para el título (ej. "seleccion_simple" -> "Selección Simple")
                const tituloFormateado = tipo.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                
                guiaTitulo.textContent = 'Guía: ' + tituloFormateado;
                guiaTexto.innerHTML = datos.guia;
            }
        }

        function cargarPlantilla() {
            const tipo = selectTipo.value;
            const datos = PLANTILLAS[tipo];
            
            if (!datos) return;

            if (campoConfig.value.trim() && campoConfig.value.trim() !== '{}') {
                if (!confirm(`¿Reemplazar la configuración actual con la plantilla por defecto para "${tipo}"?`)) return;
            }

            campoConfig.value = JSON.stringify(datos.config, null, 2);
            campoRespuesta.value = JSON.stringify(datos.respuesta, null, 2);
            actualizarPlantilla();
        }

        // Inicializar eventos
        document.addEventListener('DOMContentLoaded', () => {
            
            // ¡Importante! Escuchar cuando el usuario cambia el select
            selectTipo.addEventListener('change', actualizarPlantilla);

            // Al cargar la página, decidir si se carga todo o solo se actualiza la guía
            if (!campoConfig.value.trim() || campoConfig.value.trim() === '{}') {
                cargarPlantilla();
            } else {
                actualizarPlantilla();
            }
        });
    </script>
</x-layouts.app>