<x-layouts.app :title="__('Gestionar Conjuntos')">
    <div class="flex flex-col gap-6 p-4 lg:p-6">
        
        {{-- Header con migas de pan --}}
        <div class="flex flex-col gap-2">
            <a href="{{ route('admin.dashboard') }}" class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al Panel Principal
            </a>
            <div class="flex justify-between items-end flex-wrap gap-4">
                <h1 class="text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">Gestión de Conjuntos</h1>
                <a href="{{ route('admin.conjuntos.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-500 text-white text-sm font-bold rounded-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    + Nuevo Conjunto
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-3xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-200 px-5 py-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-3xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 px-5 py-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white/95 dark:bg-neutral-950/90 rounded-3xl shadow-xl border border-neutral-200 dark:border-neutral-800 px-4 py-5 md:px-6 md:py-6">
            <div class="mb-5">
                <div class="border-b border-neutral-200 dark:border-neutral-700">
                    <nav class="-mb-px flex flex-wrap gap-4">
                        <button class="tab-button border-b-2 border-pink-500 py-3 px-1 text-sm font-semibold text-pink-600">
                            Conjuntos
                        </button>
                    </nav>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50 dark:bg-neutral-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Nombre del Conjunto</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Preguntas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-neutral-400 uppercase tracking-wider">Sesiones</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-neutral-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @foreach($conjuntos as $conjunto)
                            <tr class="hover:bg-neutral-800/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-white">{{ $conjunto->nombre }}</span>
                                        <span class="text-xs text-neutral-500 truncate max-w-xs">{{ $conjunto->descripcion ?? 'Sin descripción' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $conjunto->activo ? 'bg-green-900/30 text-green-500' : 'bg-neutral-800 text-neutral-500' }}">
                                        {{ $conjunto->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-neutral-300">
                                    {{ $conjunto->preguntas_count }}
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-neutral-300">
                                    {{ $conjunto->sesiones_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.conjuntos.show', $conjunto) }}" 
                                        class="p-2 text-blue-500 dark:text-blue-400 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-300 hover:scale-110 hover:drop-shadow-[0_0_8px_rgba(59,130,246,0.6)]" 
                                        title="Gestionar Preguntas">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                        </a>
                                        <a href="{{ route('admin.conjuntos.edit', $conjunto) }}" 
                                        class="p-2 text-yellow-500 dark:text-yellow-400 rounded-lg transition-all duration-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 hover:text-yellow-600 dark:hover:text-yellow-300 hover:scale-110 hover:drop-shadow-[0_0_8px_rgba(234,179,8,0.6)]" 
                                        title="Editar Detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>