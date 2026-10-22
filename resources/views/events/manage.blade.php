<div class="border-t border-black/15 pt-6 space-y-4">
    <div class="flex items-center justify-between gap-4">
        <p class="text-lg font-bold text-black tracking-tight">{{ __('Gestion') }}</p>
        <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-black border-2 border-black rounded-lg font-bold text-xs text-white uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black transition-all duration-150">
            {{ __("Modifier l'événement") }}
        </a>
    </div>

    @include('events.participants', ['event' => $event, 'participants' => $confirmedParticipants])
</div>
