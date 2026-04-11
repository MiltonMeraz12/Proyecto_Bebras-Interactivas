<x-layouts.app :title="__('Nuevo Conjunto')">
    <div class="flex flex-col gap-6 p-4 lg:p-6 max-w-3xl mx-auto">

        <a href="{{ route('admin.conjuntos.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-pink-600 hover:text-pink-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Conjuntos
        </a>

        <div class="bg-white/95 dark:bg-neutral-900/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 p-6">
                <h1 class="text-3xl font-bold text-white">Crear Nuevo Conjunto</h1>
                <p class="text-white/80 mt-2">Define el nombre y asocia el recurso PDF para este conjunto.</p>
            </div>
            <div class="p-8">
                @if(session('success'))
                    <div class="rounded-3xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-200 px-5 py-4 mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="rounded-3xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 px-5 py-4 mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.conjuntos.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="nombre" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">Nombre del Conjunto</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                            class="w-full rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 px-4 py-3 text-neutral-900 dark:text-neutral-100 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 outline-none transition-all"
                            placeholder="Ej. Reto Otoño 2025">
                        @error('nombre') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">Descripción (Opcional)</label>
                        <textarea name="descripcion" id="descripcion" rows="4"
                            class="w-full rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 px-4 py-3 text-neutral-900 dark:text-neutral-100 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 outline-none transition-all"
                            placeholder="Breve explicación sobre qué trata este conjunto...">{{ old('descripcion') }}</textarea>
                    </div>

                    <div>
                        <label for="pdf_id" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">Asociar Recurso PDF</label>
                        <select name="pdf_id" id="pdf_id"
                            class="w-full rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 px-4 py-3 text-neutral-900 dark:text-neutral-100 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 outline-none transition-all">
                            <option value="">-- Sin PDF asociado --</option>
                            @foreach($pdfs as $pdf)
                                <option value="{{ $pdf->id }}" {{ old('pdf_id') == $pdf->id ? 'selected' : '' }}>
                                    {{ $pdf->nombre }} ({{ $pdf->nombre_original }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-neutral-500 mt-2">¿No encuentras el PDF? Ve a <a href="{{ route('admin.pdfs.index') }}" class="text-pink-600 underline">Gestionar PDFs</a> para subir uno nuevo.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                        <a href="{{ route('admin.conjuntos.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-neutral-300 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-950 px-5 py-3 text-sm font-semibold text-neutral-700 dark:text-neutral-200 hover:bg-neutral-200 dark:hover:bg-neutral-900 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all">
                            Guardar Conjunto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
