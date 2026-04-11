@php
    $config  = is_array($config ?? null) ? $config : [];
    $opciones = $config['opciones'] ?? [];
    // Mezclar para que no siempre aparezca en el mismo orden
    shuffle($opciones);
    $primerTipo = $opciones[0]['tipo'] ?? 'texto';
    $esGrid     = $primerTipo === 'imagen';
@endphp

<div class="grid gap-3 {{ $esGrid ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1' }}"
     id="opciones-container">
    @foreach($opciones as $opcion)
        <button
            type="button"
            class="opcion-btn text-left bg-white/80 dark:bg-neutral-800/80 border-2 border-neutral-200 dark:border-neutral-700
                   hover:border-pink-400 dark:hover:border-pink-500 rounded-2xl p-4
                   transition-all duration-200 shadow-sm hover:shadow-lg hover:scale-[1.01] group
                   {{ $progreso ? 'cursor-default' : 'cursor-pointer' }}"
            data-opcion="{{ $opcion['id'] }}"
            onclick="{{ $progreso ? '' : 'seleccionarOpcion(this)' }}">

            @if(($opcion['tipo'] ?? 'texto') === 'imagen')
                <div class="flex flex-col items-center gap-2">
                    <span class="font-extrabold text-lg bg-gradient-to-r from-yellow-500 via-pink-500 to-purple-500 bg-clip-text text-transparent">
                        {{ $opcion['id'] }})
                    </span>
                    <img src="{{ asset('storage/' . $opcion['valor']) }}"
                         alt="Opción {{ $opcion['id'] }}"
                         class="w-full max-h-40 object-contain rounded-xl">
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="font-extrabold text-lg bg-gradient-to-r from-yellow-500 via-pink-500 to-purple-500 bg-clip-text text-transparent flex-shrink-0">
                        {{ $opcion['id'] }})
                    </span>
                    <span class="text-sm text-neutral-700 dark:text-neutral-300">
                        {{ $opcion['valor'] ?? $opcion['texto'] ?? '' }}
                    </span>
                </div>
            @endif
        </button>
    @endforeach
</div>

<style>
    .opcion-btn.opcion-seleccionada {
        border-color: #ec4899 !important;
        background-color: rgba(236, 72, 153, 0.08) !important;
        box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.2);
    }
</style>