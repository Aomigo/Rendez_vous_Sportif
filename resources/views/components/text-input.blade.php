@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-2 border-black bg-white text-black text-sm font-medium focus:border-black focus:ring-2 focus:ring-black placeholder-neutral-400']) }}>
