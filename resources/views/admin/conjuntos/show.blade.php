<x-layouts.app :title="'Gestionar ' . $conjunto->nombre">
    <div class="flex flex-col gap-6 p-4 lg:p-6">
        <a href="{{ route('admin.conjuntos.index') }}" class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Conjuntos
        </a>
        
        {{-- Header / Info del Conjunto --}}
        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 px-4 py-5 md:px-6 md:py-6">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight flex items-center gap-3">
                        {{ $conjunto->nombre }}
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $conjunto->activo ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400' }}">
                            {{ $conjunto->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </h1>
                </div>
                <a href="{{ route('admin.conjuntos.edit', $conjunto) }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-200 text-sm font-semibold rounded-xl transition-colors" wire:navigate>
                    Editar Info
                </a>
            </div>
            
            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">{{ $conjunto->descripcion ?? 'Sin descripción proporcionada.' }}</p>

            @if($conjunto->pdf)
                <div class="mt-4 flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-300 bg-neutral-50 dark:bg-neutral-800/50 p-3 rounded-xl border border-neutral-200 dark:border-neutral-700 w-fit">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span><strong>PDF:</strong> {{ $conjunto->pdf->nombre_original }}</span>
                </div>
            @endif
        </div>

        {{-- Tabla de Preguntas --}}
        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 px-4 py-5 md:px-6 md:py-6 mt-6">
            
            {{-- Header / Tab section --}}
            <div class="mb-5 flex justify-between items-end border-b border-neutral-200 dark:border-neutral-700 pb-3">
                <nav class="-mb-[13px] flex flex-wrap gap-4">
                    <button class="tab-button border-b-2 border-pink-500 py-2 px-1 text-sm font-semibold text-pink-600">
                        Preguntas del Conjunto ({{ $conjunto->preguntas->count() }})
                    </button>
                </nav>
                <a href="{{ route('admin.preguntas.create', $conjunto) }}" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 hover:shadow-lg text-white font-bold text-xs px-4 py-2 rounded-xl transition-all hover:-translate-y-0.5" 
                wire:navigate>
                    + Agregar Pregunta
                </a>
            </div>
            
            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50 dark:bg-neutral-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Orden</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Dificultad</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-neutral-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @forelse($conjunto->preguntas as $pregunta)
                            <tr class="hover:bg-neutral-800/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="w-8 h-8 rounded-lg bg-neutral-800 border border-neutral-700 flex items-center justify-center text-white text-xs font-bold shadow-inner">
                                        #{{ $pregunta->numero ?? $pregunta->orden }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $pregunta->titulo }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                        {{ $pregunta->dificultad === 'Baja' ? 'bg-green-900/30 text-green-500' : '' }}
                                        {{ $pregunta->dificultad === 'Media' ? 'bg-yellow-900/30 text-yellow-500' : '' }}
                                        {{ $pregunta->dificultad === 'Alta' ? 'bg-red-900/30 text-red-500' : '' }}">
                                        {{ $pregunta->dificultad }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-semibold text-neutral-400">
                                    {{ str_replace('_', ' ', $pregunta->tipo_interaccion) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.preguntas.edit', [$conjunto, $pregunta]) }}" 
                                        class="p-2 text-blue-500 dark:text-blue-400 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-300 hover:scale-110 hover:drop-shadow-[0_0_8px_rgba(59,130,246,0.6)]" 
                                        title="Editar Pregunta">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.preguntas.destroy', [$conjunto, $pregunta]) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas quitar esta pregunta?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-red-500 dark:text-red-400 rounded-lg transition-all duration-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-300 hover:scale-110 hover:drop-shadow-[0_0_8px_rgba(239,68,68,0.6)]" 
                                                    title="Eliminar Pregunta">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-neutral-500 font-medium">No hay preguntas registradas en este conjunto.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>