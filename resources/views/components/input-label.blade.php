@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-xs uppercase tracking-wider text-black']) }}>
    {{ $value ?? $slot }}
</label>
