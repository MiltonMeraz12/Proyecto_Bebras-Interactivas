<script>
    function obtenerRespuesta() {
        // Verificar si es el formato de computadoras (pregunta 23)
        const computadorasEstados = document.querySelectorAll('.computadora-estado:checked');
        
        if (computadorasEstados.length > 0) {
            // Formato para pregunta 23: [{computadora: 1, estado: 'virus_rojo'}, ...]
            const respuesta = Array.from(computadorasEstados).map(radio => ({
                computadora: parseInt(radio.dataset.computadora),
                estado: radio.dataset.estado
            }));
            return respuesta.length > 0 ? respuesta : null;
        }
        
        // Formato estándar: array de IDs seleccionados
        const checkboxes = document.querySelectorAll('input[name="opciones[]"]:checked');
        const respuesta = Array.from(checkboxes).map(cb => cb.value);
        return respuesta.length > 0 ? respuesta : null;
    }

    function deshabilitarInteraccion() {
        // Deshabilitar computadoras
        document.querySelectorAll('.computadora-estado').forEach(radio => {
            radio.disabled = true;
            radio.parentElement.classList.add('opacity-50', 'cursor-not-allowed');
        });
        
        // Deshabilitar opciones estándar
        document.querySelectorAll('input[name="opciones[]"]').forEach(cb => {
            cb.disabled = true;
            cb.parentElement.classList.add('opacity-50', 'cursor-not-allowed');
        });
    }
</script>