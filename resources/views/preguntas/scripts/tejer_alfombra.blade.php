<script>
    let simboloSeleccionado = null;
    let letraSeleccionada = null;
    let alfombra = {}; // { "fila-columna": simbolo }
    const config = @json($config ?? []);
    const filas = config.filas || 6;
    const columnas = config.columnas || 6;
    const simbolosDisponibles = config.simbolos_disponibles || ['purple', 'red', 'yellow', 'green'];

    // Mapeo de símbolos a clases CSS
    const simbolosMap = {
        'purple': { letra: 'M', nombre: 'Morado', color: 'bg-purple-500', border: 'border-purple-600', text: 'text-white' },
        'red': { letra: 'R', nombre: 'Rojo', color: 'bg-red-500', border: 'border-red-600', text: 'text-white' },
        'yellow': { letra: 'A', nombre: 'Amarillo', color: 'bg-yellow-400', border: 'border-yellow-600', text: 'text-gray-800' },
        'green': { letra: 'V', nombre: 'Verde', color: 'bg-green-500', border: 'border-green-600', text: 'text-white' },
    };

    document.addEventListener('DOMContentLoaded', function() {
        actualizarResumen();
    });

    function seleccionarSimbolo(simbolo, letra) {
        simboloSeleccionado = simbolo;
        letraSeleccionada = letra;
        actualizarEstadoBotonesSimbolo();
    }

    function actualizarEstadoBotonesSimbolo() {
        document.querySelectorAll('.simbolo-btn').forEach(btn => {
            if (btn.dataset.simbolo === simboloSeleccionado) {
                btn.classList.add('seleccionado');
            } else {
                btn.classList.remove('seleccionado');
            }
        });
    }

    function colorearCelda(celda) {
        if (!simboloSeleccionado) {
            mostrarMensaje('Por favor, selecciona un símbolo primero.', 'warning');
            return;
        }

        const fila = parseInt(celda.dataset.fila);
        const columna = parseInt(celda.dataset.columna);
        const clave = `${fila}-${columna}`;

        // Obtener información del símbolo
        const simboloInfo = simbolosMap[simboloSeleccionado] || { letra: '?', color: 'bg-gray-500', border: 'border-gray-600', text: 'text-white' };

        // Aplicar estilo a la celda
        celda.style.backgroundColor = '';
        celda.style.borderColor = '';
        celda.classList.remove('bg-gray-50');
        celda.classList.add(simboloInfo.color, simboloInfo.border, 'coloreada');
        
        // Ocultar coordenadas y mostrar símbolo
        const coordenadas = celda.querySelector('.celda-coordenadas');
        const simbolo = celda.querySelector('.celda-simbolo');
        if (coordenadas) {
            coordenadas.style.display = 'none';
        }
        if (simbolo) {
            simbolo.textContent = simboloInfo.letra;
            simbolo.classList.remove('hidden');
            simbolo.classList.add(simboloInfo.text);
        }

        // Guardar en alfombra
        alfombra[clave] = simboloInfo.letra;

        actualizarResumen();
        mostrarMensaje(`Celda (${fila}, ${columna}) marcada con ${simboloInfo.nombre}`, 'success');
    }

    function actualizarResumen() {
        const resumen = document.getElementById('resumen-alfombra');
        const completadas = Object.keys(alfombra).length;
        const total = filas * columnas;

        if (completadas === 0) {
            resumen.innerHTML = '<p>Ninguna celda completada aún.</p>';
        } else {
            resumen.innerHTML = `<p>${completadas} de ${total} celdas completadas (${Math.round((completadas/total)*100)}%)</p>`;
        }
    }

    function mostrarMensaje(texto, tipo) {
        let mensajeDiv = document.getElementById('mensaje-temporal');
        if (!mensajeDiv) {
            mensajeDiv = document.createElement('div');
            mensajeDiv.id = 'mensaje-temporal';
            mensajeDiv.className = 'fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm';
            document.body.appendChild(mensajeDiv);
        }

        const colores = {
            'info': 'bg-blue-100 text-blue-800 border-blue-300',
            'success': 'bg-green-100 text-green-800 border-green-300',
            'warning': 'bg-yellow-100 text-yellow-800 border-yellow-300'
        };

        mensajeDiv.className = `fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm border-2 ${colores[tipo] || colores.info}`;
        mensajeDiv.textContent = texto;

        setTimeout(() => {
            if (mensajeDiv) {
                mensajeDiv.style.opacity = '0';
                mensajeDiv.style.transition = 'opacity 0.5s';
                setTimeout(() => {
                    if (mensajeDiv && mensajeDiv.parentNode) {
                        mensajeDiv.parentNode.removeChild(mensajeDiv);
                    }
                }, 500);
            }
        }, 3000);
    }

    function obtenerRespuesta() {
        // Verificar que todas las celdas estén completadas
        const total = filas * columnas;
        const completadas = Object.keys(alfombra).length;
        
        if (completadas !== total) {
            alert(`Por favor completa todas las celdas. Faltan ${total - completadas} celda(s).`);
            return null;
        }

        // Convertir a formato esperado: array 2D [['M', 'M', ...], ...]
        const respuesta = [];
        for (let fila = 1; fila <= filas; fila++) {
            const filaArray = [];
            for (let col = 1; col <= columnas; col++) {
                const clave = `${fila}-${col}`;
                filaArray.push(alfombra[clave] || '');
            }
            respuesta.push(filaArray);
        }

        return respuesta;
    }

    function deshabilitarInteraccion() {
        document.querySelectorAll('.simbolo-btn').forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });
        document.querySelectorAll('.celda-alfombra').forEach(celda => {
            celda.classList.add('cursor-not-allowed');
            celda.style.pointerEvents = 'none';
        });
    }

    // Cargar respuesta previa si existe
    @if(isset($progresoUsuario) && $progresoUsuario && $progresoUsuario->respuesta_usuario)
        const respuestaPrevia = @json($progresoUsuario->respuesta_usuario);
        if (respuestaPrevia && Array.isArray(respuestaPrevia)) {
            respuestaPrevia.forEach((fila, filaIndex) => {
                if (Array.isArray(fila)) {
                    fila.forEach((simbolo, colIndex) => {
                        if (simbolo) {
                            // Encontrar el símbolo correspondiente
                            const simboloKey = Object.keys(simbolosMap).find(key => simbolosMap[key].letra === simbolo);
                            if (simboloKey) {
                                const filaNum = filaIndex + 1;
                                const colNum = colIndex + 1;
                                const celda = document.querySelector(`.celda-alfombra[data-fila="${filaNum}"][data-columna="${colNum}"]`);
                                if (celda) {
                                    seleccionarSimbolo(simboloKey, simbolo);
                                    colorearCelda(celda);
                                }
                            }
                        }
                    });
                }
            });
        }
    @endif
</script>

