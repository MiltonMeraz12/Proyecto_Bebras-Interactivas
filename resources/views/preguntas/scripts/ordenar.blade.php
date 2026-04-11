<script>
    var ordenActual = [];

    // Funciones globales (llamadas desde show.blade.php)
    function obtenerRespuesta() {
        return ordenActual.length > 0 ? ordenActual : null;
    }

    function deshabilitarInteraccion() {
        if (typeof Sortable === 'undefined') return;
        const fuente = document.getElementById('elementos-fuente');
        const destino = document.getElementById('area-ordenamiento');

        if (fuente && Sortable.get(fuente)) {
            Sortable.get(fuente).option('disabled', true);
        }

        if (destino && Sortable.get(destino)) {
            Sortable.get(destino).option('disabled', true);
        }

        document.querySelectorAll('.elemento-draggable').forEach(el => {
            el.classList.remove('cursor-move');
            el.classList.add('cursor-not-allowed', 'opacity-75');
        });
    }

    function initOrdenar() {
        if (typeof Sortable === 'undefined') {
            setTimeout(initOrdenar, 50);
            return;
        }
        const fuente = document.getElementById('elementos-fuente');
        const destino = document.getElementById('area-ordenamiento');
        if (!fuente || !destino) return;

        if (Sortable.get(fuente)) Sortable.get(fuente).destroy();
        if (Sortable.get(destino)) Sortable.get(destino).destroy();

        function actualizarOrden() {
            ordenActual = [];
            const elementos = destino.querySelectorAll('.elemento-draggable');
            elementos.forEach(el => {
                ordenActual.push(el.dataset.id);
            });
            const placeholder = document.getElementById('placeholder-orden');
            if (placeholder) {
                placeholder.style.display = ordenActual.length === 0 ? 'block' : 'none';
            }
        }

        function actualizarFuente() {
            const elementosEnFuente = fuente.querySelectorAll('.elemento-draggable');
            const contadorFuente = document.getElementById('contador-fuente');
            if (contadorFuente) contadorFuente.textContent = `(${elementosEnFuente.length})`;
            
            const elementosEnDestino = destino.querySelectorAll('.elemento-draggable');
            const contadorDestino = document.getElementById('contador-destino');
            if (contadorDestino) contadorDestino.textContent = `(${elementosEnDestino.length})`;
            
            const mensaje = fuente.querySelector('.mensaje-vacio');
            if (elementosEnFuente.length === 0) {
                if (!mensaje) {
                    const msg = document.createElement('p');
                    msg.className = 'mensaje-vacio text-neutral-400 text-center py-4 italic text-sm';
                    msg.textContent = 'Todos los elementos han sido movidos';
                    fuente.appendChild(msg);
                }
            } else if (mensaje) {
                mensaje.remove();
            }
        }

        Sortable.create(fuente, {
            group: { name: 'shared', pull: true, put: true },
            animation: 150,
            sort: false,
            draggable: '.elemento-draggable',
            ghostClass: 'opacity-40',
            onAdd: actualizarOrden,
            onRemove: actualizarOrden
        });

        Sortable.create(destino, {
            group: 'shared',
            animation: 150,
            draggable: '.elemento-draggable',
            ghostClass: 'opacity-40',
            onAdd: function(evt) {
                actualizarOrden();
                actualizarFuente();
            },
            onUpdate: actualizarOrden,
            onRemove: function(evt) {
                actualizarOrden();
                actualizarFuente();
            }
        });

        actualizarFuente();
    }

    initOrdenar();

    document.addEventListener('livewire:navigated', function() {
        initOrdenar();
    });
</script>