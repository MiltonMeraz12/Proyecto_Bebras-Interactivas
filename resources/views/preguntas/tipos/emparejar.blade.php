@php
    $config = is_array($config ?? null) ? $config : [];
    $objetos = $config['objetos'] ?? [];
    $destinos = $config['destinos'] ?? [];
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- Objetos --}}
    <div>
        <h4 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-3">Objetos:</h4>
        <div class="space-y-3">
            @foreach($objetos as $objeto)
                <div class="flex items-center gap-3 p-2 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-200 dark:border-neutral-700">
                    @if(!empty($objeto['imagen']))
                        <img src="{{ asset('storage/' . $objeto['imagen']) }}"
                             alt="{{ $objeto['nombre'] }}"
                             class="w-12 h-12 object-contain flex-shrink-0 rounded-lg">
                    @endif
                    <span class="text-sm font-medium flex-1 min-w-0 truncate text-neutral-800 dark:text-neutral-200">
                        {{ $objeto['nombre'] }}
                    </span>
                    <select class="emparejamiento flex-shrink-0 min-h-[44px] border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-xl px-3 py-1.5 text-sm text-neutral-800 dark:text-neutral-200 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all"
                            data-objeto="{{ $objeto['id'] }}">
                        <option value="">Selecciona...</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino['id'] }}">{{ $destino['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Destinos (visual) --}}
    <div>
        <h4 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-3">Destinos:</h4>
        <div class="space-y-3">
            @foreach($destinos as $destino)
                <div class="p-3 bg-neutral-100 dark:bg-neutral-800 border-2 border-neutral-300 dark:border-neutral-600 rounded-xl">
                    <span class="font-semibold text-sm text-neutral-800 dark:text-neutral-200">
                        {{ $destino['nombre'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>