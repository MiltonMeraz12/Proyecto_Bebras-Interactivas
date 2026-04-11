<script>
    let piezasColocadas = {}; // { "fila-columna": { piezaId, color } }
    const configRP = @json($config ?? []);
    const piezasDisponibles = configRP.piezas_disponibles || [];
    const estructuraRP = configRP.estructura || [];

    document.addEventListener('DOMContentLoaded', function() {
        inicializarRompecabezas();
    });

    function inicializarRompecabezas() {
        const esTactil = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
        const celdasRP  = document.querySelectorAll('.celda-hexagono[data-fija="false"]');

        if (!esTactil) {
            // Drag & drop en escritorio
            document.querySelectorAll('.pieza-item').forEach(pieza => {
                pieza.addEventListener('dragstart', e => {
                    e.dataTransfer.setData('text/plain', pieza.dataset.piezaId);
                    pieza.classList.add('dragging');
                });
                pieza.addEventListener('dragend', () => pieza.classList.remove('dragging'));
            });

            celdasRP.forEach(celda => {
                celda.addEventListener('dragover', e => { e.preventDefault(); celda.classList.add('drag-over'); });
                celda.addEventListener('dragleave', ()  => celda.classList.remove('drag-over'));
                celda.addEventListener('drop', e => {
                    e.preventDefault();
                    celda.classList.remove('drag-over');
                    const piezaId = e.dataTransfer.getData('text/plain');
                    if (!piezaId || celda.dataset.ocupada === 'true') return;
                    colocarPiezaEnCelda(celda, piezaId);
                });
                celda.addEventListener('dblclick', () => {
                    if (celda.dataset.ocupada === 'true') removerPiezaDeCelda(celda);
                });
            });
        } else {
            // Modo táctil
            window._piezaSeleccionadaRP = null;
            const instrArr = document.querySelector('.instrucciones-arrastre-rp');
            const instrToque = document.querySelector('.instrucciones-toque-rp');
            if (instrArr)   instrArr.style.display   = 'none';
            if (instrToque) instrToque.style.display = 'block';

            document.querySelectorAll('.pieza-item').forEach(pieza => {
                pieza.addEventListener('touchend', e => {
                    e.preventDefault();
                    if (pieza.style.display === 'none') return;
                    const id = pieza.dataset.piezaId;
                    if (window._piezaSeleccionadaRP === id) {
                        window._piezaSeleccionadaRP = null;
                        pieza.classList.remove('pieza-toque-activa');
                    } else {
                        document.querySelectorAll('.pieza-item').forEach(p => p.classList.remove('pieza-toque-activa'));
                        window._piezaSeleccionadaRP = id;
                        pieza.classList.add('pieza-toque-activa');
                    }
                });
            });

            celdasRP.forEach(celda => {
                celda.addEventListener('touchend', e => {
                    e.preventDefault();
                    if (window._piezaSeleccionadaRP) {
                        if (celda.dataset.ocupada === 'true') return;
                        colocarPiezaEnCelda(celda, window._piezaSeleccionadaRP);
                        document.querySelectorAll('.pieza-item').forEach(p => p.classList.remove('pieza-toque-activa'));
                        window._piezaSeleccionadaRP = null;
                    } else if (celda.dataset.ocupada === 'true') {
                        removerPiezaDeCelda(celda);
                    }
                });
            });
        }
    }

    function colocarPiezaEnCelda(celda, piezaId) {
        const pieza = piezasDisponibles.find(p => p.id === piezaId);
        if (!pieza) return;

        const fila    = celda.dataset.fila;
        const columna = celda.dataset.columna;
        const clave   = `${fila}-${columna}`;

        celda.dataset.ocupada   = 'true';
        celda.dataset.piezaId   = piezaId;
        celda.style.backgroundColor = pieza.color;
        celda.style.borderColor     = pieza.color;

        const contenedor = celda.querySelector('.pieza-en-celda');
        const span       = celda.querySelector('span');
        if (span)      span.style.display = 'none';
        if (contenedor) {
            contenedor.classList.remove('hidden');
            const img = contenedor.querySelector('.pieza-en-celda-imagen');
            const lbl = contenedor.querySelector('span');
            if (pieza.imagen && img) { img.src = `/storage/${pieza.imagen}`; img.classList.remove('hidden'); }
            if (lbl) lbl.textContent = pieza.id;
        }

        piezasColocadas[clave] = { piezaId, color: pieza.color };

        // Ocultar pieza de la lista
        const piezaEl = document.querySelector(`.pieza-item[data-pieza-id="${piezaId}"]`);
        if (piezaEl) piezaEl.style.display = 'none';

        actualizarContadoresRP();
    }

    function removerPiezaDeCelda(celda) {
        const fila    = celda.dataset.fila;
        const columna = celda.dataset.columna;
        const clave   = `${fila}-${columna}`;
        const piezaId = celda.dataset.piezaId;

        celda.dataset.ocupada       = 'false';
        delete celda.dataset.piezaId;
        celda.style.backgroundColor = '';
        celda.style.borderColor     = '';

        const contenedor = celda.querySelector('.pieza-en-celda');
        const span       = celda.querySelector('span');
        if (span)      span.style.display = '';
        if (contenedor) contenedor.classList.add('hidden');

        delete piezasColocadas[clave];

        const piezaEl = document.querySelector(`.pieza-item[data-pieza-id="${piezaId}"]`);
        if (piezaEl) piezaEl.style.display = '';

        actualizarContadoresRP();
    }

    function actualizarContadoresRP() {
        const total    = document.querySelectorAll('.celda-hexagono[data-fija="false"]').length;
        const colocadas= Object.keys(piezasColocadas).length;
        const el = document.getElementById('contador-colocadas');
        if (el) el.textContent = `(${colocadas}/${total} colocadas)`;

        const resumen = document.getElementById('resumen-colocacion');
        if (!resumen) return;
        if (colocadas === 0) { resumen.innerHTML = '<p>Ninguna pieza colocada aún.</p>'; return; }
        let html = '<div class="grid grid-cols-2 md:grid-cols-4 gap-2">';
        Object.entries(piezasColocadas).forEach(([pos, datos]) => {
            html += `<div class="bg-white border border-gray-300 rounded p-1 text-xs">
                <span class="font-semibold">Pieza ${datos.piezaId}</span> → ${pos}</div>`;
        });
        html += '</div>';
        resumen.innerHTML = html;
    }

    function obtenerRespuesta() {
        const totalCeldas  = document.querySelectorAll('.celda-hexagono[data-fija="false"]').length;
        const totalPiezas  = piezasDisponibles.length;
        const colocadas    = Object.keys(piezasColocadas).length;

        if (colocadas < totalPiezas) {
            alert(`Por favor coloca todas las piezas. Faltan ${totalPiezas - colocadas}.`);
            return null;
        }

        return Object.entries(piezasColocadas).map(([pos, datos]) => {
            const [fila, columna] = pos.split('-').map(Number);
            return { pieza: datos.piezaId, fila, columna };
        });
    }

    function deshabilitarInteraccion() {
        document.querySelectorAll('.pieza-item').forEach(p => {
            p.draggable = false;
            p.classList.add('opacity-50', 'cursor-not-allowed');
            p.style.pointerEvents = 'none';
        });
        document.querySelectorAll('.celda-hexagono[data-fija="false"]').forEach(c => {
            c.style.pointerEvents = 'none';
        });
    }

    // Cargar respuesta previa
    @if(isset($progreso) && $progreso && $progreso->respuesta_usuario)
        const respuestaPreviaRP = @json($progreso->respuesta_usuario);
        if (Array.isArray(respuestaPreviaRP)) {
            document.addEventListener('DOMContentLoaded', () => {
                respuestaPreviaRP.forEach(item => {
                    const celda = document.querySelector(
                        `.celda-hexagono[data-fila="${item.fila}"][data-columna="${item.columna}"][data-fija="false"]`
                    );
                    if (celda) colocarPiezaEnCelda(celda, String(item.pieza));
                });
            });
        }
    @endif
</script>