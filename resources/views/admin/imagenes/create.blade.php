<x-layouts.app :title="__('Subir Imagen')">
    <div class="flex flex-col gap-6 p-4 lg:p-6 max-w-2xl mx-auto">

        <a href="{{ route('admin.imagenes.index') }}"
           class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors w-fit"
           wire:navigate>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Imágenes
        </a>

        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 px-6 py-5">
                <h1 class="text-2xl font-extrabold text-white">Subir nueva imagen</h1>
                <p class="text-white/80 text-sm mt-1">Las imágenes subidas se pueden usar como enunciados o explicaciones de preguntas.</p>
            </div>

            <div class="px-6 py-6 md:px-8 md:py-8">
                @if($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-2xl px-5 py-4 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.imagenes.store') }}"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none transition-all"
                               placeholder="Ej: Tabla de libros - Pregunta 01">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Texto alternativo (alt)</label>
                        <input type="text" name="alt" value="{{ old('alt') }}"
                               class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-4 py-3 text-sm text-neutral-900 dark:text-white focus:border-pink-500 outline-none"
                               placeholder="Descripción breve de la imagen para accesibilidad">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Archivo de imagen *</label>
                        <div class="border-2 border-dashed border-neutral-300 dark:border-neutral-700 rounded-xl p-6 text-center hover:border-pink-400 dark:hover:border-pink-600 transition-colors">
                            <input type="file" name="archivo" accept="image/*" required
                                   class="w-full text-sm text-neutral-500 dark:text-neutral-400
                                          file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-pink-50 file:text-pink-700
                                          hover:file:bg-pink-100
                                          dark:file:bg-pink-900/30 dark:file:text-pink-400
                                          cursor-pointer"
                                   onchange="previsualizarImagen(this)">
                            <p class="text-xs text-neutral-400 mt-3">JPG, PNG, GIF, WebP · Máximo <strong>5 MB</strong></p>
                            <img id="preview-imagen" src="" alt="Vista previa"
                                 class="hidden mt-4 max-h-48 mx-auto rounded-xl object-contain border border-neutral-200 dark:border-neutral-700">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('admin.imagenes.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-neutral-300 dark:border-neutral-700 px-5 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                           wire:navigate>
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                            Subir imagen
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Instrucción de uso --}}
        <div class="bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-2xl p-4 text-sm text-neutral-800 dark:text-neutral-200">
            <p class="font-semibold mb-1">¿Cómo usar esta imagen en una pregunta?</p>
            <p>Una vez subida, copia la <strong>ruta</strong> que aparece en la galería y pégala en el campo <code>imagen_enunciado</code> o <code>imagen_explicacion</code> al crear o editar una pregunta.</p>
            <p class="mt-1">Para imágenes dentro de la configuración JSON (ej. opciones con imagen): usa la ruta como valor del campo <code>"valor"</code> y el tipo <code>"imagen"</code>.</p>
        </div>
    </div>

    <script>
        function previsualizarImagen(input) {
            const preview = document.getElementById('preview-imagen');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layouts.app>