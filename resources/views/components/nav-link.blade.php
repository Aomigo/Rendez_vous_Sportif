@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-4 border-black text-xs font-bold uppercase tracking-wider leading-5 text-black focus:outline-none focus:border-black transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-xs font-semibold uppercase tracking-wider leading-5 text-neutral-600 hover:text-black hover:border-black/50 focus:outline-none focus:text-black focus:border-black transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
