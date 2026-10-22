@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-black text-start text-sm font-bold uppercase tracking-wider text-black bg-neutral-100 focus:outline-none focus:bg-neutral-200 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-sm font-semibold uppercase tracking-wider text-neutral-600 hover:text-black hover:bg-neutral-100 hover:border-black/50 focus:outline-none focus:text-black focus:bg-neutral-100 focus:border-black transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
