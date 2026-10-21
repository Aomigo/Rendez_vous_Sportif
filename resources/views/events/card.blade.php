<div class="bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all duration-150 flex flex-col overflow-hidden">
    <div class="p-6 flex-1 flex flex-col justify-between">
        <div>
            <!-- Sport & Places restantes -->
            <div class="flex justify-between items-center mb-3">
                <span class="inline-block bg-black text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                    {{ $event->sport->name ?? 'Général' }}
                </span>
                <span class="text-xs font-bold text-black border border-black bg-neutral-100 px-2.5 py-1 rounded-full">
                    {{ $event->remaining_seats }} place{{ $event->remaining_seats > 1 ? 's' : '' }}
                </span>
            </div>

            <!-- Titre -->
            <h3 class="text-lg font-black text-black mb-3 tracking-tight">
                {{ $event->title }}
            </h3>

            <!-- Métadonnées : Date/Heure, Ville -->
            <div class="text-sm text-neutral-700 space-y-1.5">
                <p class="flex items-center gap-2">
                    <span class="font-bold text-black uppercase text-xs tracking-wider">Date :</span> 
                    <span>{{ $event->starts_at->translatedFormat('d F Y à H:i') }}</span>
                </p>
                <p class="flex items-center gap-2">
                    <span class="font-bold text-black uppercase text-xs tracking-wider">Ville :</span> 
                    <span>{{ $event->city }}</span>
                </p>
                @if($event->venue)
                    <p class="flex items-center gap-2">
                        <span class="font-bold text-black uppercase text-xs tracking-wider">Lieu :</span> 
                        <span>{{ $event->venue }}</span>
                    </p>
                @endif
            </div>
        </div>

        <!-- Collectif organisateur -->
        <div class="mt-5 pt-3 border-t border-black/15 text-xs text-neutral-600 font-medium">
            Organisé par : <span class="font-bold text-black">{{ $event->organization->name ?? 'Indépendant' }}</span>
        </div>
    </div>
</div>
