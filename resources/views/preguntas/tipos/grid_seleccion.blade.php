@php
    $config        = is_array($config ?? null) ? $config : [];
    $labelsCol     = $config['labels_columnas'] ?? [];
    $labelsFila    = $config['labels_filas'] ?? [];
    $estadoInicial = $config['estado_inicial'] ?? [];
    $numerosCeldas = $config['numeros_celdas'] ?? [];
@endphp

<div class="overflow-x-auto">
    <div class="inline-block min-w-full">
        {{-- Labels de columnas --}}
        <div class="flex mb-1 pl-12">
            @foreach($labelsCol as $col)
                <div class="w-12 text-center font-bold text-xs text-neutral-600 dark:text-neutral-400">{{ $col }}</div>
            @endforeach
        </div>

        {{-- Grid --}}
        @foreach($estadoInicial as $iF => $fila)
            <div class="flex items-center mb-1">
                <div class="w-12 text-center font-bold text-xs text-neutral-600 dark:text-neutral-400">
                    {{ $labelsFila[$iF] ?? $iF }}
                </div>

                @foreach($fila as $iC => $activa)
                    <button
                        type="button"
                        class="celda w-12 h-12 border-2 rounded-xl transition-all mr-1 relative
                               {{ $activa ? 'bg-pink-500 border-pink-600' : 'bg-neutral-100 dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 hover:bg-neutral-200' }}
                               {{ $progreso ? 'cursor-default' : 'cursor-pointer' }}"
                        data-fila="{{ $iF }}"
                        data-columna="{{ $iC }}"
                        data-activa="{{ $activa ? '1' : '0' }}"
                        onclick="{{ $progreso ? '' : 'toggleCelda(this)' }}">

                        <span class="absolute top-0.5 left-1 text-xs font-medium text-neutral-500">
                            {{ $numerosCeldas[$iF][$iC] ?? '' }}
                        </span>

                        @if($activa)
                            <span class="text-2xl font-bold text-white celda-x">×</span>
                        @endif
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>
</div>