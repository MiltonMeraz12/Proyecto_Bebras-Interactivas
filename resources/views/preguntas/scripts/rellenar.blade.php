<script>
    let colorSeleccionado = null;
    let respuestas = {}; // { areaId: color }
    const config = @json($config ?? []);
    const areas = config.areas || [];

    // Mapeo de colores para la respuesta (debe coincidir con el formato del seeder)
    const colorMapRespuesta = {
        'green': 'verde',
        'yellow': 'amarillo',
        'blue': 'azul'
    };

    function seleccionarColor(color) {
        colorSeleccionado = color;
        
        // Actualizar UI de botones
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.classList.remove('seleccionado');
        });
        event.target.classList.add('seleccionado');
        
        // Mostrar mensaje
        mostrarMensaje(`Color seleccionado: ${colorMapRespuesta[color] || color}. Ahora selecciona un área.`, 'info');
    }

    function seleccionarArea(areaId) {
        if (!colorSeleccionado) {
            mostrarMensaje('Por favor, selecciona un color primero.', 'warning');
            return;
        }

        // Guardar respuesta
        respuestas[areaId] = colorSeleccionado;
        
        // Actualizar preview visual
        const preview = document.getElementById(`preview-${areaId}`);
        const areaItem = document.querySelector(`[data-area-id="${areaId}"]`);
        
        if (preview) {
            const colorClases = {
                'green': 'bg-green-500 border-green-700',
                'yellow': 'bg-yellow-400 border-yellow-600',
                'blue': 'bg-blue-500 border-blue-700'
            };
            
            preview.className = `color-preview w-12 h-12 rounded-full border-2 ${colorClases[colorSeleccionado] || 'bg-gray-500'} coloreado`;
            preview.innerHTML = '';
        }
        
        if (areaItem) {
            areaItem.classList.add('seleccionada');
        }
        
        mostrarMensaje(`Área "${obtenerNombreArea(areaId)}" coloreada con ${colorMapRespuesta[colorSeleccionado] || colorSeleccionado}.`, 'success');
        
        // Verificar si todas las áreas están coloreadas
        verificarCompletitud();
    }

    function obtenerNombreArea(areaId) {
        const area = areas.find(a => a.id === areaId);
        return area ? area.nombre : areaId;
    }

    function verificarCompletitud() {
        const todasColoreadas = areas.every(area => respuestas[area.id]);
        if (todasColoreadas) {
            mostrarMensaje('¡Todas las áreas han sido coloreadas! Puedes verificar tu respuesta.', 'success');
        }
    }

    function mostrarMensaje(texto, tipo) {
        // Crear o actualizar mensaje temporal
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
        
        // Auto-ocultar después de 3 segundos
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
        // Verificar que todas las áreas estén coloreadas
        const areasFaltantes = areas.filter(area => !respuestas[area.id]);
        
        if (areasFaltantes.length > 0) {
            const nombres = areasFaltantes.map(a => a.nombre).join(', ');
            alert(`Por favor colorea todas las áreas. Faltan: ${nombres}`);
            return null;
        }
        
        // Convertir respuestas al formato esperado: [{area: 'id', color: 'verde'}, ...]
        const respuesta = areas.map(area => ({
            area: area.id,
            color: colorMapRespuesta[respuestas[area.id]] || respuestas[area.id]
        }));
        
        return respuesta;
    }

    function deshabilitarInteraccion() {
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });
        
        document.querySelectorAll('.area-item').forEach(item => {
            item.style.pointerEvents = 'none';
            item.classList.add('opacity-50');
        });
    }

    // Inicializar: si ya hay respuestas previas, cargarlas
    @if(isset($progresoUsuario) && $progresoUsuario && $progresoUsuario->respuesta_usuario)
        const respuestaPrevia = @json($progresoUsuario->respuesta_usuario);
        if (respuestaPrevia && Array.isArray(respuestaPrevia)) {
            respuestaPrevia.forEach(item => {
                if (item.area && item.color) {
                    // Convertir color en español a inglés para mostrar
                    const colorIngles = {
                        'verde': 'green',
                        'amarillo': 'yellow',
                        'azul': 'blue'
                    }[item.color] || item.color;
                    
                    respuestas[item.area] = colorIngles;
                    
                    // Actualizar UI
                    const preview = document.getElementById(`preview-${item.area}`);
                    const areaItem = document.querySelector(`[data-area-id="${item.area}"]`);
                    
                    if (preview) {
                        const colorClases = {
                            'green': 'bg-green-500 border-green-700',
                            'yellow': 'bg-yellow-400 border-yellow-600',
                            'blue': 'bg-blue-500 border-blue-700'
                        };
                        preview.className = `color-preview w-12 h-12 rounded-full border-2 ${colorClases[colorIngles] || 'bg-gray-500'} coloreado`;
                        preview.innerHTML = '';
                    }
                    
                    if (areaItem) {
                        areaItem.classList.add('seleccionada');
                    }
                }
            });
        }
    @endif
</script>
