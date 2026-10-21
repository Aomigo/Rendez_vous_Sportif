<div class="bg-white rounded-lg shadow hover:shadow-md transition flex flex-col overflow-hidden border border-gray-100">
    <div class="p-5 flex-1 flex flex-col justify-between">
        <div>
            <!-- Sport & Places restantes -->
            <div class="flex justify-between items-center mb-2">
<span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
    {{ $event->sport->name ?? 'Général' }}
</span>
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                    {{ $event->remaining_seats }} place(s) restante(s)
                </span>
            </div>

            <!-- Titre -->
            <h3 class="text-lg font-bold text-gray-900 mb-2">
                {{ $event->title }}
            </h3>

            <!-- Métadonnées : Date/Heure, Ville, Organisateur -->
            <div class="text-sm text-gray-600 space-y-1">
                <p>
                    <span class="font-medium">Date :</span> 
                    {{ $event->starts_at->translatedFormat('d F Y à H:i') }}
                </p>
                <p>
                    <span class="font-medium">Ville :</span> 
                    {{ $event->city }}
                </p>
            </div>
        </div>

        <!-- Collectif organisateur -->
<div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500">
    Organisé par : <span class="font-medium text-gray-700">{{ $event->organization->name ?? 'Indépendant' }}</span>
</div>
    </div>
</div>