@php
    $config  = is_array($config ?? null) ? $config : [];
    $opciones = $config['opciones'] ?? [];
    $tieneComputadoras = isset($config['computadoras']);
    if (!$tieneComputadoras) shuffle($opciones);
@endphp

@if($tieneComputadoras)
    @php
        $numComputadoras = $config['computadoras'] ?? 12;
        $opcionesPorComputadora = $config['opciones_por_computadora'] ?? ['virus_rojo', 'virus_azul', 'desconectada'];
        $estadosMap = [
            'virus_rojo'   => ['nombre' => 'Virus Rojo',   'color' => 'text-red-600',  'bg' => 'bg-red-50 dark:bg-red-900/20'],
            'virus_azul'   => ['nombre' => 'Virus Azul',   'color' => 'text-blue-600', 'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
            'desconectada' => ['nombre' => 'Desconectada', 'color' => 'text-neutral-600', 'bg' => 'bg-neutral-50 dark:bg-neutral-800/50'],
        ];
    @endphp
    <div class="space-y-3">
        @for($i = 1; $i <= $numComputadoras; $i++)
            <div class="border-2 border-neutral-200 dark:border-neutral-700 rounded-2xl p-4">
                <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                    Computadora {{ $i }}:
                </p>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($opcionesPorComputadora as $opcion)
                        @php $info = $estadosMap[$opcion] ?? ['nombre' => ucfirst($opcion), 'color' => 'text-neutral-600', 'bg' => 'bg-neutral-50']; @endphp
                        <label class="flex items-center gap-2 p-2 border-2 border-neutral-300 dark:border-neutral-600 rounded-xl hover:border-pink-400 cursor-pointer transition-all {{ $info['bg'] }} min-h-[44px]">
                            <input type="radio"
                                   name="computadora_{{ $i }}"
                                   value="{{ $opcion }}"
                                   class="w-4 h-4 text-pink-600 computadora-estado"
                                   data-computadora="{{ $i }}"
                                   data-estado="{{ $opcion }}"
                                   {{ $progreso ? 'disabled' : '' }}>
                            <span class="text-xs font-semibold {{ $info['color'] }}">{{ $info['nombre'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endfor
    </div>
@else
    <div class="space-y-2">
        @foreach($opciones as $opcion)
            <label class="flex items-center gap-3 p-3 border-2 border-neutral-200 dark:border-neutral-700
                          hover:border-pink-400 dark:hover:border-pink-500 rounded-2xl cursor-pointer
                          transition-all min-h-[44px] {{ $progreso ? 'cursor-default' : '' }}">
                <input type="checkbox"
                       name="opciones[]"
                       value="{{ $opcion['id'] }}"
                       class="w-4 h-4 text-pink-600 rounded focus:ring-pink-500"
                       {{ $progreso ? 'disabled' : '' }}>
                <span class="text-sm text-neutral-800 dark:text-neutral-200">
                    <span class="font-bold text-pink-500 mr-1">{{ $opcion['id'] }})</span>
                    {{ $opcion['valor'] ?? '' }}
                </span>
            </label>
        @endforeach
    </div>
    @if(isset($config['nota']))
        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2 italic">ℹ️ {{ $config['nota'] }}</p>
    @endif
@endif