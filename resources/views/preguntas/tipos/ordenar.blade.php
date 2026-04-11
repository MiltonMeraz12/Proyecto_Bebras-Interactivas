@php
    $config   = is_array($config ?? null) ? $config : [];
    $elementos = $config['elementos'] ?? array_map(fn($o) => [
        'id'     => $o['id'],
        'nombre' => $o['valor'] ?? $o['nombre'] ?? $o['id'],
    ], $config['opciones'] ?? []);

    $mezclados = $elementos;
    shuffle($mezclados);
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- Disponibles --}}
    <div>
        <h4 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2 flex items-center gap-2">
            Disponibles
            <span class="text-xs font-normal text-neutral-400" id="contador-fuente">({{ count($mezclados) }})</span>
        </h4>
        <div id="elementos-fuente"
             class="bg-neutral-100 dark:bg-neutral-800/50 border-2 border-dashed border-neutral-300 dark:border-neutral-600 rounded-2xl p-2 min-h-[160px] max-h-[320px] overflow-y-auto space-y-1">
            @foreach($mezclados as $el)
                <div class="elemento-draggable bg-white dark:bg-neutral-800 border-2 border-neutral-300 dark:border-neutral-600
                             rounded-xl p-3 cursor-move hover:shadow-md hover:border-pink-300 dark:hover:border-pink-600
                             transition-all touch-manipulation"
                     data-id="{{ $el['id'] }}">
                    @if(isset($el['imagen']))
                        <img src="{{ asset('storage/' . $el['imagen']) }}"
                             alt="{{ $el['id'] }}"
                             class="w-full h-14 object-contain rounded-lg">
                    @else
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                            {{ $el['nombre'] ?? $el['id'] }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Área de ordenamiento --}}
    <div>
        <h4 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2 flex items-center gap-2">
            Orden correcto
            <span class="text-xs font-normal text-neutral-400" id="contador-destino">(0)</span>
        </h4>
        <div id="area-ordenamiento"
             class="bg-pink-50 dark:bg-pink-900/10 border-2 border-dashed border-pink-300 dark:border-pink-700 rounded-2xl p-2 min-h-[160px] max-h-[320px] overflow-y-auto">
            <p class="text-xs text-neutral-400 text-center py-6" id="placeholder-orden">
                Arrastra los elementos aquí en orden
            </p>
        </div>
    </div>
</div>

{{-- SortableJS se carga desde el script --}}