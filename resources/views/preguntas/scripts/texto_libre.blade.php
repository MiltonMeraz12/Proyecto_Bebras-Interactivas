
<script>
    function obtenerRespuesta() {
        const input = document.getElementById('respuesta-numero');
        const valor = input.value.trim();
        return valor ? [valor] : null;
    }

    function deshabilitarInteraccion() {
        const input = document.getElementById('respuesta-numero');
        input.disabled = true;
        input.classList.add('opacity-50', 'cursor-not-allowed');
    }
</script>