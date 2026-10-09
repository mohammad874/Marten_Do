@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' · ' : '' }}Marten Flow</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="h-full font-sans text-slate-900 antialiased">
    <div
        x-data="{ sidebarOpen: false }"
        x-on:keydown.escape.window="sidebarOpen = false"
        class="min-h-full"
    >
        {{-- Mobile backdrop: closes the drawer on tap. --}}
        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity.duration.200ms
            x-on:click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden"
            aria-hidden="true"
        ></div>

        <x-sidebar />

        <div class="lg:pl-72">
            {{-- Mobile top bar: the only way to open the drawer below `lg`. --}}
            <header class="sticky top-0 z-30 flex h-14 items-center gap-x-3 border-b border-slate-200 bg-white px-4 lg:hidden">
                <button
                    type="button"
                    class="-ml-2 rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500"
                    aria-controls="primary-sidebar"
                    x-bind:aria-expanded="sidebarOpen.toString()"
                    x-on:click="sidebarOpen = true"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                </button>

                <span class="text-sm font-semibold text-slate-900">Marten Flow</span>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
