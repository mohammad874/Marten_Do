@props([
    'href',
    'active' => false,
    'hint' => null,
])

<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->class([
        'group flex items-center gap-x-3 rounded-lg px-3 py-2 transition-colors',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400',
        'bg-white/10 text-white' => $active,
        'text-slate-300 hover:bg-white/5 hover:text-white' => ! $active,
    ]) }}
>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.75"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        @class([
            'h-5 w-5 shrink-0 transition-colors',
            'text-amber-400' => $active,
            'text-slate-400 group-hover:text-slate-200' => ! $active,
        ])
    >
        {{ $icon }}
    </svg>

    <span class="min-w-0 flex-1">
        <span class="block text-sm font-medium leading-snug">{{ $slot }}</span>
        @if ($hint)
            <span class="block truncate text-xs text-slate-500 group-hover:text-slate-400">{{ $hint }}</span>
        @endif
    </span>
</a>
