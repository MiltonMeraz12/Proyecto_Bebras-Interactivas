<script>
    function obtenerRespuesta() {
        const inputNum  = document.getElementById('respuesta-numero');
        const inputText = document.getElementById('respuesta-texto');

        if (inputNum) {
            const valor = inputNum.value.trim();
            return valor !== '' ? [valor] : null;
        }

        if (inputText) {
            const valor = inputText.value.trim();
            return valor !== '' ? [valor] : null;
        }

        return null;
    }

    function deshabilitarInteraccion() {
        const inputNum  = document.getElementById('respuesta-numero');
        const inputText = document.getElementById('respuesta-texto');
        if (inputNum)  { inputNum.disabled  = true; inputNum.classList.add('opacity-50');  }
        if (inputText) { inputText.disabled = true; inputText.classList.add('opacity-50'); }
    }
</script>