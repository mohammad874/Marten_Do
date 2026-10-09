@props([
    'href',
    'active' => false,
    'hint' => null,
])

{{--
    Brand colour usage (kept off small text: white on the brand reds is only
    ~3.7-4.0:1, so the reds carry icons, the active bar and focus rings, while
    labels stay white / light slate on the dark sidebar):
      Red 1 (#EE3A25)  active indicator bar
      Red 2 (#EB4C40)  icons (active + hover), focus ring
--}}
<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->class([
        'group relative flex items-center gap-x-3 rounded-lg px-3 py-2 transition-colors',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-red-2',
        'bg-brand-red-1/15 text-white before:absolute before:inset-y-2 before:left-0 before:w-[3px] before:rounded-full before:bg-brand-red-1' => $active,
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
            'text-brand-red-2' => $active,
            'text-slate-400 group-hover:text-brand-red-2' => ! $active,
        ])
    >
        {{ $icon }}
    </svg>

    <span class="min-w-0 flex-1">
        <span class="block text-sm font-medium leading-snug">{{ $slot }}</span>
        @if ($hint)
            <span class="block truncate text-xs text-slate-400 group-hover:text-slate-300">{{ $hint }}</span>
        @endif
    </span>
</a>
