<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <div class="ms-2 me-5 flex items-center gap-2 lg:ms-0">
                <x-app-logo />
            </div>

            <flux:navbar class="-mb-px max-lg:hidden">
                {{-- Visible SOLO para Administradores --}}
                @if(auth()->user()?->isAdmin())
                    <flux:navbar.item icon="layout-grid"
                        :href="route('admin.dashboard')"
                        :current="request()->routeIs('admin.dashboard')"
                        wire:navigate>
                        {{ __('Panel Administrativo') }}
                    </flux:navbar.item>
                    <flux:navbar.item icon="folder"
                        :href="route('admin.conjuntos.index')"
                        :current="request()->routeIs('admin.conjuntos.*')"
                        wire:navigate>
                        {{ __('Gestionar Conjuntos') }}
                    </flux:navbar.item>
                    <flux:navbar.item icon="document"
                        :href="route('admin.pdfs.index')"
                        :current="request()->routeIs('admin.pdfs.*')"
                        wire:navigate>
                        {{ __('Gestionar PDFs') }}
                    </flux:navbar.item>
                    <flux:navbar.item icon="photo"
                        :href="route('admin.imagenes.index')"
                        :current="request()->routeIs('admin.imagenes.*')"
                        wire:navigate>
                        {{ __('Gestionar Imágenes') }}
                    </flux:navbar.item>
                @endif

                @if(auth()->user() && !auth()->user()->isAdmin())
                    <flux:navbar.item icon="book-open"
                        :href="route('conjuntos.index')"
                        :current="request()->routeIs('conjuntos.*') && !request()->routeIs('recursos.*')"
                        wire:navigate>
                        {{ __('Mis conjuntos') }}
                    </flux:navbar.item>
                    <flux:navbar.item icon="document"
                        :href="route('recursos.index')"
                        :current="request()->routeIs('recursos.*')"
                        wire:navigate>
                        {{ __('Recursos PDF') }}
                    </flux:navbar.item>
                @endif
            </flux:navbar>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white font-bold">
                                    {{ auth()->user()->initials() }}
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <div class="ms-1 flex items-center gap-2">
                <x-app-logo />
            </div>

            <flux:navlist variant="outline">
                
                {{-- Menú Móvil SOLO para Administradores --}}
                @if(auth()->user()?->isAdmin())
                    <flux:navlist.group :heading="__('Administración')">
                        <flux:navlist.item icon="layout-grid" 
                            :href="route('admin.dashboard')" 
                            :current="request()->routeIs('admin.dashboard')" 
                            wire:navigate>
                            {{ __('Panel Administrativo') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="folder" 
                            :href="route('admin.conjuntos.index')" 
                            :current="request()->routeIs('admin.conjuntos.*')" 
                            wire:navigate>
                            {{ __('Gestionar Conjuntos') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="document" 
                            :href="route('admin.pdfs.index')" 
                            :current="request()->routeIs('admin.pdfs.*')" 
                            wire:navigate>
                            {{ __('Gestionar PDFs') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="photo" 
                            :href="route('admin.imagenes.index')" 
                            :current="request()->routeIs('admin.imagenes.*')" 
                            wire:navigate>
                            {{ __('Gestionar Imágenes') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif

                {{-- Menú Móvil SOLO para Alumnos --}}
                @if(auth()->user() && !auth()->user()->isAdmin())
                    <flux:navlist.group :heading="__('Bebras Lab')">
                        <flux:navlist.item icon="book-open" 
                            :href="route('conjuntos.index')" 
                            :current="request()->routeIs('conjuntos.*') && !request()->routeIs('recursos.*')" 
                            wire:navigate>
                            {{ __('Mis conjuntos') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="document" 
                            :href="route('recursos.index')" 
                            :current="request()->routeIs('recursos.*')" 
                            wire:navigate>
                            {{ __('Recursos PDF') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
                
            </flux:navlist>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
