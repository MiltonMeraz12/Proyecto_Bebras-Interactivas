<x-layouts.app :title="__('Editar Conjunto')">
    <div class="flex flex-col gap-6 p-4 lg:p-6 max-w-3xl mx-auto w-full">

        {{-- Migas de pan --}}
        <a href="{{ route('admin.conjuntos.show', $conjunto) }}" class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a detalles del conjunto
        </a>

        {{-- Contenedor Principal --}}
        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 px-6 py-6 md:px-8 md:py-8">
            
            {{-- Header del Formulario --}}
            <div class="border-b border-neutral-200 dark:border-neutral-800 pb-5 mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                        Editar Conjunto
                    </h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                        Modificando detalles de <span class="font-semibold text-white">"{{ $conjunto->nombre }}"</span>
                    </p>
                </div>
            </div>

            {{-- Alertas de Sesión --}}
            @if(session('success'))
                <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 mb-6 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 mb-6 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Formulario Principal --}}
            <form action="{{ route('admin.conjuntos.update', $conjunto) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="nombre" class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                        Nombre del Conjunto
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $conjunto->nombre) }}" required
                        class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none transition-all placeholder-neutral-400 dark:placeholder-neutral-600">
                </div>

                <div>
                    <label for="descripcion" class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                        Descripción
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                        class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none transition-all placeholder-neutral-400 dark:placeholder-neutral-600">{{ old('descripcion', $conjunto->descripcion) }}</textarea>
                </div>

                <div>
                    <label for="pdf_id" class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                        Recurso PDF Asociado
                    </label>
                    <select name="pdf_id" id="pdf_id"
                        class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none transition-all">
                        <option value="">-- Sin PDF asociado --</option>
                        @foreach($pdfs as $pdf)
                            <option value="{{ $pdf->id }}" {{ old('pdf_id', $conjunto->pdf_id) == $pdf->id ? 'selected' : '' }}>
                                {{ $pdf->nombre }} ({{ $pdf->nombre_original }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Zona de Acciones --}}
                <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-6 mt-8 border-t border-neutral-200 dark:border-neutral-800">
                    
                    {{-- Botón Eliminar (Peligro) --}}
                    <button type="button"
                            onclick="if(confirm('¿Seguro que quieres eliminar este conjunto? Todas sus preguntas asociadas se borrarán y no se podrá recuperar.')) document.getElementById('delete-form').submit();"
                            class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition-colors px-2 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eliminar Conjunto
                    </button>

                    {{-- Botones de Guardar/Cancelar --}}
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.conjuntos.show', $conjunto) }}" 
                           class="inline-flex items-center justify-center rounded-xl border border-neutral-300 dark:border-neutral-700 px-5 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-center">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5 text-center">
                            Actualizar Cambios
                        </button>
                    </div>
                </div>
            </form>

            {{-- Formulario oculto para eliminar --}}
            <form id="delete-form" action="{{ route('admin.conjuntos.destroy', $conjunto) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
            
        </div>
    </div>
</x-layouts.app>