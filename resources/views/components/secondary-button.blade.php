<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border-2 border-black rounded-lg font-bold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 disabled:opacity-25 transition-all duration-150 bg-white text-black hover:bg-neutral-100']) }}>
    {{ $slot }}
</button>
