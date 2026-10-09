{{--
    Marten Flow primary sidebar.

    Every item that sits behind the Corporate Veil is wrapped in @can, so the
    markup (label, hint and URL) is never sent to a user without the explicit
    Spatie permission. Hiding a link is not access control on its own: each
    target route must also carry the matching `can:` middleware.

    Expects an Alpine.js scope that provides `sidebarOpen` (see
    <x-layouts.dashboard>) to drive the off-canvas drawer below `lg`.
--}}
@php
    $user = auth()->user();

    $roleName = $user->getRoleNames()->first();
    $roleLabel = match ($roleName) {
        'ctmo' => 'CTMO',
        'coo' => 'COO',
        null => 'No role assigned',
        default => \Illuminate\Support\Str::headline($roleName),
    };

    $initials = \Illuminate\Support\Str::of($user->name)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
@endphp

<aside
    id="primary-sidebar"
    x-bind:class="{ 'translate-x-0': sidebarOpen, '-translate-x-full invisible': ! sidebarOpen }"
    class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col bg-slate-900 invisible transition-[transform,translate,visibility] duration-200 ease-in-out lg:visible lg:translate-x-0"
    aria-label="Sidebar"
>
    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 px-5">
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-x-3 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-red-2"
        >
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-red-1 text-xl font-bold text-white" aria-hidden="true">M</span>
            <span class="leading-tight">
                <span class="block text-sm font-semibold text-white">Marten Flow</span>
                <span class="block text-xs text-slate-400">Marten Do Operations</span>
            </span>
        </a>

        <button
            type="button"
            class="-mr-2 rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-brand-red-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-red-2 lg:hidden"
            x-on:click="sidebarOpen = false"
        >
            <span class="sr-only">Close sidebar</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5" aria-label="Primary">
        {{-- 1. Dashboard Home: every authenticated user. --}}
        <ul role="list" class="space-y-1">
            <li>
                <x-sidebar.link :href="route('dashboard')" :active="request()->routeIs('dashboard')" hint="Overview">
                    <x-slot:icon>
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </x-slot:icon>
                    Dashboard Home
                </x-sidebar.link>
            </li>
        </ul>

        {{-- 2. Strategic Intelligence: CTMO and COO only. --}}
        @can('view_strategic_metrics')
            <div>
                <h2 class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Executive</h2>
                <ul role="list" class="space-y-1">
                    <li>
                        <x-sidebar.link :href="route('strategic.index')" :active="request()->routeIs('strategic.*')" hint="CAC · LTV · Runway">
                            <x-slot:icon>
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                                <polyline points="17 6 23 6 23 12" />
                            </x-slot:icon>
                            Strategic Intelligence
                        </x-sidebar.link>
                    </li>
                </ul>
            </div>
        @endcan

        {{-- 3-5. Operations: fleet, driver cash and support. --}}
        @canany(['manage_driver_shifts', 'collect_driver_cash', 'view_support_tickets'])
            <div>
                <h2 class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Operations</h2>
                <ul role="list" class="space-y-1">
                    @can('manage_driver_shifts')
                        <li>
                            <x-sidebar.link :href="route('fleet.index')" :active="request()->routeIs('fleet.*')" hint="Drivers & shifts">
                                <x-slot:icon>
                                    <rect x="1" y="3" width="15" height="13" />
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                                    <circle cx="5.5" cy="18.5" r="2.5" />
                                    <circle cx="18.5" cy="18.5" r="2.5" />
                                </x-slot:icon>
                                Fleet &amp; Shift Management
                            </x-sidebar.link>
                        </li>
                    @endcan

                    @can('collect_driver_cash')
                        <li>
                            <x-sidebar.link :href="route('driver-cash.index')" :active="request()->routeIs('driver-cash.*')" hint="Daily collections">
                                <x-slot:icon>
                                    <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                                    <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
                                </x-slot:icon>
                                Driver Cash Hand-in
                            </x-sidebar.link>
                        </li>
                    @endcan

                    @can('view_support_tickets')
                        <li>
                            <x-sidebar.link :href="route('support.index')" :active="request()->routeIs('support.*')" hint="Tickets & rescue">
                                <x-slot:icon>
                                    <circle cx="12" cy="12" r="10" />
                                    <circle cx="12" cy="12" r="4" />
                                    <line x1="4.93" y1="4.93" x2="9.17" y2="9.17" />
                                    <line x1="14.83" y1="9.17" x2="19.07" y2="4.93" />
                                    <line x1="14.83" y1="14.83" x2="19.07" y2="19.07" />
                                    <line x1="4.93" y1="19.07" x2="9.17" y2="14.83" />
                                </x-slot:icon>
                                Customer Support Desk
                            </x-sidebar.link>
                        </li>
                    @endcan
                </ul>
            </div>
        @endcanany

        {{-- 6. Operational Ledger & Cashflow: Accountant (and executives). --}}
        @can('view_daily_cashflow')
            <div>
                <h2 class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Finance</h2>
                <ul role="list" class="space-y-1">
                    <li>
                        <x-sidebar.link :href="route('ledger.index')" :active="request()->routeIs('ledger.*')" hint="Cash flow & OpEx">
                            <x-slot:icon>
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                            </x-slot:icon>
                            Operational Ledger &amp; Cashflow
                        </x-sidebar.link>
                    </li>
                </ul>
            </div>
        @endcan
    </nav>

    {{-- Signed-in user --}}
    <div class="shrink-0 border-t border-white/10 p-4">
        <div class="flex items-center gap-x-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-700 text-sm font-semibold text-white ring-2 ring-brand-red-1" aria-hidden="true">{{ $initials }}</span>

            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ $roleLabel }}</p>
            </div>

            @if (\Illuminate\Support\Facades\Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-brand-red-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-red-2"
                    >
                        <span class="sr-only">Sign out</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</aside>
