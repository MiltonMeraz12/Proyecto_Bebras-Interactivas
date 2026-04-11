@php
    $config = is_array($config ?? null) ? $config : [];
    $esNumero = ($config['tipo_respuesta'] ?? '') === 'numero';
@endphp

<div class="w-full max-w-md mx-auto">
    @if($esNumero)
        <label class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300">
            Ingresa tu respuesta (número):
        </label>
        <input
            type="number"
            id="respuesta-numero"
            min="{{ $config['min'] ?? 0 }}"
            max="{{ $config['max'] ?? 999999 }}"
            class="w-full px-4 py-3 text-xl text-center border-2 border-neutral-300 dark:border-neutral-600
                   rounded-2xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200
                   dark:bg-neutral-800 dark:text-white transition-all"
            placeholder="0"
            {{ $progreso ? 'disabled' : '' }}>
    @else
        <label class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300">
            Ingresa tu respuesta:
        </label>
        <textarea
            id="respuesta-texto"
            rows="3"
            class="w-full px-4 py-3 border-2 border-neutral-300 dark:border-neutral-600
                   rounded-2xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200
                   dark:bg-neutral-800 dark:text-white text-sm transition-all resize-none"
            placeholder="Escribe tu respuesta aquí..."
            {{ $progreso ? 'disabled' : '' }}></textarea>
    @endif
</div>