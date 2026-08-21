@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'group flex items-center gap-3 pl-2.5 pr-3 py-2.5 text-sm font-semibold rounded-xl border-l-4 border-indigo-600 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition duration-150'
            : 'group flex items-center gap-3 pl-2.5 pr-3 py-2.5 text-sm font-medium rounded-xl border-l-4 border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <span class="shrink-0 w-5 h-5 {{ ($active ?? false) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors">
            {!! $icon !!}
        </span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
