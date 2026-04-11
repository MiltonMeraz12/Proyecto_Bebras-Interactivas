<x-layouts.app :title="__('PDFs / Recursos')">
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
                <h1 class="text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">
                    PDFs / Recursos
                </h1>
                <a href="{{ route('admin.pdfs.create') }}"
                   class="px-5 py-2.5 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 text-white text-sm font-bold rounded-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all"
                   wire:navigate>
                    + Subir PDF
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-3xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-3xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-5 py-4 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50 dark:bg-neutral-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Tamaño</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Conjuntos</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Subido</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-neutral-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                        @forelse($pdfs as $pdf)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-lg flex-shrink-0">
                                            📄
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-neutral-900 dark:text-white">{{ $pdf->nombre }}</p>
                                            <p class="text-xs text-neutral-400">{{ $pdf->nombre_original }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ $pdf->tamanioLegible() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                        {{ $pdf->conjuntos_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ $pdf->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.pdfs.show', $pdf) }}"
                                           class="p-2 text-blue-500 dark:text-blue-400 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:scale-110"
                                           title="Ver / Descargar"
                                           wire:navigate>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('admin.pdfs.destroy', $pdf) }}"
                                              class="inline"
                                              onsubmit="return confirm('¿Eliminar este PDF? Solo puedes hacerlo si no tiene conjuntos asociados.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-red-500 dark:text-red-400 rounded-lg transition-all duration-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:scale-110"
                                                    title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div class="text-4xl mb-3">📭</div>
                                    <p class="text-neutral-400 font-medium">No hay PDFs subidos aún.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>