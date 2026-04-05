<script>
    let colorSeleccionado = null;
    let coloreados = {}; // { "fila-columna": color }
    const config = @json($config ?? []);
    const coloresDisponibles = config.colores_disponibles || ['verde', 'amarillo', 'azul'];
    const hexagonosIniciales = config.hexagonos_iniciales || [];
    const filas = config.filas || 5;

    // Mapeo de colores a valores CSS
    const colorMap = {
        'verde': { bg: '#10b981', border: '#059669' },
        'amarillo': { bg: '#facc15', border: '#eab308' },
        'azul': { bg: '#3b82f6', border: '#2563eb' }
    };

    // Inicializar colores fijos
    hexagonosIniciales.forEach(hex => {
        const fila = hex.posicion[0];
        const col = hex.posicion[1];
        coloreados[`${fila}-${col}`] = hex.color;
    });

    document.addEventListener('DOMContentLoaded', function() {
        actualizarResumen();
        actualizarContadores();
    });

    function seleccionarColor(color) {
        colorSeleccionado = color;
        actualizarEstadoBotonesColor();
    }

    function actualizarEstadoBotonesColor() {
        document.querySelectorAll('.color-btn').forEach(btn => {
            if (btn.dataset.color === colorSeleccionado) {
                btn.classList.add('seleccionado');
            } else {
                btn.classList.remove('seleccionado');
            }
        });
    }

    function colorearHexagono(celda) {
        if (!colorSeleccionado) {
            mostrarMensaje('Por favor, selecciona un color primero.', 'warning');
            return;
        }

        if (celda.dataset.fija === 'true') {
            mostrarMensaje('Este hexágono ya está coloreado y no se puede cambiar.', 'warning');
            return;
        }

        const fila = parseInt(celda.dataset.fila);
        const columna = parseInt(celda.dataset.columna);

        // Validar regla del triángulo antes de colorear
        if (!validarTriangulo(fila, columna, colorSeleccionado)) {
            mostrarMensaje('Esta colocación no cumple la regla del triángulo. Todas las piezas deben ser del mismo color O todas de colores diferentes.', 'warning');
            return;
        }

        // Colorear el hexágono
        const colorInfo = colorMap[colorSeleccionado] || { bg: '#gray', border: '#gray' };
        celda.style.backgroundColor = colorInfo.bg;
        celda.style.borderColor = colorInfo.border;
        celda.dataset.color = colorSeleccionado;
        celda.classList.add('coloreado');
        
        // Ocultar número de celda
        const numeroCelda = celda.querySelector('span');
        if (numeroCelda) {
            numeroCelda.style.display = 'none';
        }

        // Guardar colocación
        coloreados[`${fila}-${columna}`] = colorSeleccionado;

        actualizarResumen();
        actualizarContadores();
        mostrarMensaje(`Hexágono coloreado con ${colorSeleccionado}`, 'success');
    }

    function validarTriangulo(fila, columna, colorNuevo) {
        // La regla: el triángulo formado (hexágono nuevo + 2 de abajo) debe tener:
        // - Todas del mismo color O
        // - Todas de colores diferentes
        
        // Obtener las 2 piezas de abajo (fila + 1, columna y columna + 1)
        const piezaAbajo1 = obtenerColorEnPosicion(fila + 1, columna);
        const piezaAbajo2 = obtenerColorEnPosicion(fila + 1, columna + 1);
        
        // Si no hay 2 piezas de abajo, no se puede validar (pero se permite colorear)
        if (!piezaAbajo1 || !piezaAbajo2) {
            return true; // Se permite si no hay piezas de abajo aún
        }
        
        const colores = [colorNuevo, piezaAbajo1, piezaAbajo2];
        
        // Verificar si todas son del mismo color
        const todosIguales = colores.every(c => c === colores[0]);
        
        // Verificar si todas son de colores diferentes
        const todosDiferentes = new Set(colores).size === 3;
        
        return todosIguales || todosDiferentes;
    }

    function obtenerColorEnPosicion(fila, columna) {
        const clave = `${fila}-${columna}`;
        if (coloreados[clave]) {
            return coloreados[clave];
        }
        
        // También verificar si hay una pieza fija en esa posición
        const celda = document.querySelector(`.hexagono-celda[data-fila="${fila}"][data-columna="${columna}"][data-fija="true"]`);
        if (celda) {
            return celda.dataset.color;
        }
        
        return null;
    }

    function actualizarResumen() {
        const resumen = document.getElementById('resumen-colocacion');
        const coloreadosUsuario = Object.keys(coloreados).length - hexagonosIniciales.length;

        if (coloreadosUsuario === 0) {
            resumen.innerHTML = '<p>Ningún hexágono coloreado aún.</p>';
        } else {
            let html = '<div class="grid grid-cols-2 md:grid-cols-4 gap-2">';
            Object.entries(coloreados).forEach(([posicion, color]) => {
                // Solo mostrar los que no son fijos
                if (!hexagonosIniciales.some(h => `${h.posicion[0]}-${h.posicion[1]}` === posicion)) {
                    html += `<div class="bg-white border border-gray-300 rounded p-1 text-xs">
                        <span class="font-semibold">${posicion}</span> → <span class="capitalize">${color}</span>
                    </div>`;
                }
            });
            html += '</div>';
            resumen.innerHTML = html;
        }
    }

    function actualizarContadores() {
        const totalCeldas = document.querySelectorAll('.hexagono-celda[data-fija="false"]').length;
        const coloreadosUsuario = Object.keys(coloreados).length - hexagonosIniciales.length;

        const contadorColoreados = document.getElementById('contador-coloreados');
        if (contadorColoreados) {
            contadorColoreados.textContent = `(${coloreadosUsuario}/${totalCeldas} coloreados)`;
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
        // Convertir coloreados a formato esperado: [{posicion: [fila, columna], color: 'verde'}, ...]
        const respuesta = [];
        
        Object.entries(coloreados).forEach(([posicion, color]) => {
            // Solo incluir los que no son fijos
            if (!hexagonosIniciales.some(h => `${h.posicion[0]}-${h.posicion[1]}` === posicion)) {
                const [fila, columna] = posicion.split('-').map(Number);
                respuesta.push({
                    posicion: [fila, columna],
                    color: color
                });
            }
        });

        return respuesta.length > 0 ? respuesta : null;
    }

    function deshabilitarInteraccion() {
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });
        document.querySelectorAll('.hexagono-celda').forEach(celda => {
            celda.classList.add('cursor-not-allowed');
            celda.style.pointerEvents = 'none';
        });
    }

    // Cargar respuesta previa si existe
    @if(isset($progresoUsuario) && $progresoUsuario && $progresoUsuario->respuesta_usuario)
        const respuestaPrevia = @json($progresoUsuario->respuesta_usuario);
        if (respuestaPrevia && Array.isArray(respuestaPrevia)) {
            respuestaPrevia.forEach(item => {
                if (item.posicion && item.color) {
                    const [fila, columna] = item.posicion;
                    const celda = document.querySelector(`.hexagono-celda[data-fila="${fila}"][data-columna="${columna}"]`);
                    if (celda && celda.dataset.fija !== 'true') {
                        seleccionarColor(item.color);
                        colorearHexagono(celda);
                    }
                }
            });
        }
    @endif
</script>

