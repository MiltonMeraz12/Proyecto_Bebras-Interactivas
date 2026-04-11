<x-layouts.app :title="__('Gestión de Imágenes')">
    <div class="flex flex-col gap-6 p-4 lg:p-6">

        <div class="flex flex-col gap-2">
            <a href="{{ route('admin.dashboard') }}"
               class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors w-fit"
               wire:navigate>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Panel
            </a>
            <div class="flex justify-between items-end flex-wrap gap-4">
                <h1 class="text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">Imágenes</h1>
                <a href="{{ route('admin.imagenes.create') }}"
                   class="px-5 py-2.5 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 text-white text-sm font-bold rounded-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all"
                   wire:navigate>
                    + Subir imagen
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-3xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Galería --}}
        @if($imagenes->isEmpty())
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 p-12 text-center">
                <div class="text-5xl mb-4">🖼️</div>
                <p class="text-neutral-500 dark:text-neutral-400 font-medium">No hay imágenes subidas aún.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($imagenes as $imagen)
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow overflow-hidden group">
                        <div class="aspect-square overflow-hidden bg-neutral-100 dark:bg-neutral-800">
                            <img src="{{ $imagen->url() }}"
                                 alt="{{ $imagen->alt }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-3">
                            <p class="text-xs font-semibold text-neutral-800 dark:text-white truncate" title="{{ $imagen->nombre }}">
                                {{ $imagen->nombre }}
                            </p>
                            <p class="text-xs text-neutral-400 mt-0.5">{{ $imagen->tamanioLegible() }}</p>

                            {{-- Ruta copiable (para usar en config de preguntas) --}}
                            <div class="mt-2 flex items-center gap-1">
                                <input type="text"
                                       value="{{ $imagen->ruta }}"
                                       readonly
                                       class="flex-1 text-xs bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-2 py-1 font-mono text-neutral-600 dark:text-neutral-300 truncate"
                                       title="Ruta para usar en configuración JSON"
                                       onclick="this.select(); document.execCommand('copy'); mostrarToast('Ruta copiada')">
                                <button onclick="navigator.clipboard.writeText('{{ $imagen->ruta }}').then(()=>mostrarToast('¡Copiado!'))"
                                        class="flex-shrink-0 p-1 text-pink-600 hover:text-pink-700 dark:text-pink-400"
                                        title="Copiar ruta">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('admin.imagenes.destroy', $imagen) }}"
                                  class="mt-2"
                                  onsubmit="return confirm('¿Eliminar esta imagen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full text-xs text-red-500 hover:text-red-700 font-semibold py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div id="toast"
         class="fixed bottom-6 right-6 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 text-sm font-semibold px-4 py-2 rounded-xl shadow-xl opacity-0 transition-opacity pointer-events-none z-50">
    </div>

    <script>
        function mostrarToast(msg) {
            const el = document.getElementById('toast');
            el.textContent = msg;
            el.style.opacity = '1';
            setTimeout(() => { el.style.opacity = '0'; }, 2000);
        }
    </script>
</x-layouts.app>