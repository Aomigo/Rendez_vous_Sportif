<article class="relative flex flex-col justify-between overflow-hidden rounded-xl border-2 border-black bg-white p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
    
    <div>
        <!-- Sport & Places restantes -->
        <div class="mb-3 flex items-center justify-between">
            <span class="inline-block rounded-full bg-black px-3 py-1 text-xs font-bold uppercase tracking-wider text-white">
                {{ $event->sport->name ?? 'Général' }}
            </span>
            <span class="rounded-full border border-black bg-neutral-100 px-2.5 py-1 text-xs font-bold text-black">
                {{ $event->remaining_seats }} {{ Str::plural('place', $event->remaining_seats) }}
            </span>
        </div>

        <!-- Titre avec lien étendu à toute la carte -->
        <h3 class="mb-3 text-lg font-black tracking-tight text-black">
            <a href="/e/{{ $event->public_token }}" class="after:absolute after:inset-0 focus:outline-none">
                {{ $event->title }}
            </a>
        </h3>

        <!-- Métadonnées : Date/Heure, Ville, Lieu -->
        <div class="space-y-1.5 text-sm text-neutral-700">
            <p class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-black">Date :</span> 
                <time datetime="{{ $event->starts_at->toIso8601String() }}">
                    {{ $event->starts_at->translatedFormat('d F Y à H:i') }}
                </time>
            </p>
            <p class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-black">Ville :</span> 
                <span>{{ $event->city }}</span>
            </p>
            @if($event->venue)
                <p class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-black">Lieu :</span> 
                    <span>{{ $event->venue }}</span>
                </p>
            @endif
        </div>
    </div>

    <!-- Collectif organisateur -->
    <div class="mt-5 border-t border-black/15 pt-3 text-xs font-medium text-neutral-600">
        Organisé par : <span class="font-bold text-black">{{ $event->organization->name ?? 'Indépendant' }}</span>
    </div>

</article>