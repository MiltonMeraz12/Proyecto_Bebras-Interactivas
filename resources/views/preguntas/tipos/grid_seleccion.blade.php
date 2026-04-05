
<div class="overflow-x-auto">
    <div class="inline-block min-w-full">
        {{-- Labels de columnas --}}
        <div class="flex mb-1 pl-12">
            @foreach($pregunta->configuracion['labels_columnas'] as $col)
                <div class="w-12 text-center font-bold text-xs text-gray-700">{{ $col }}</div>
            @endforeach
        </div>

        {{-- Grid --}}
        @foreach($pregunta->configuracion['estado_inicial'] as $indexFila => $fila)
            <div class="flex items-center mb-1">
                <div class="w-12 text-center font-bold text-xs text-gray-700">
                    {{ $pregunta->configuracion['labels_filas'][$indexFila] }}
                </div>
                
                @foreach($fila as $indexCol => $celda)
                    <button 
                        type="button"
                        class="celda w-12 h-12 border-2 rounded-lg transition-all mr-1 relative
                               {{ $celda ? 'bg-red-500 border-red-600' : 'bg-gray-100 border-gray-300 hover:bg-gray-200' }}"
                        data-fila="{{ $indexFila }}"
                        data-columna="{{ $indexCol }}"
                        data-activa="{{ $celda }}"
                        onclick="toggleCelda(this)">
                        
                        <span class="absolute top-0.5 left-1 text-xs font-medium text-gray-600">
                            {{ $pregunta->configuracion['numeros_celdas'][$indexFila][$indexCol] }}
                        </span>
                        
                        @if($celda)
                            <span class="text-2xl font-bold text-white celda-x">×</span>
                        @endif
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>
</div>