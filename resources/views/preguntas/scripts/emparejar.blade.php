
<script>
    function obtenerRespuesta() {
        const selects = document.querySelectorAll('.emparejamiento');
        const respuesta = [];
        let completo = true;

        selects.forEach(select => {
            if (select.value) {
                respuesta.push({
                    objeto: select.dataset.objeto,
                    destino: select.value
                });
            } else {
                completo = false;
            }
        });

        return completo ? respuesta : null;
    }

    function deshabilitarInteraccion() {
        document.querySelectorAll('.emparejamiento').forEach(select => {
            select.disabled = true;
            select.classList.add('opacity-50', 'cursor-not-allowed');
        });
    }
</script>