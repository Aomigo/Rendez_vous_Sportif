<form method="GET" action="{{ route('events.search') }}" class="bg-white p-6 rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Filtre Sport -->
        <div>
            <label for="sport" class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">Sport</label>
            <select name="sport" id="sport" class="block w-full rounded-lg border-2 border-black bg-white text-black text-sm font-medium focus:border-black focus:ring-2 focus:ring-black shadow-sm">
                <option value="">Tous les sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->name }}" @selected(request('sport') == $sport->name)>
                        {{ $sport->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filtre Ville -->
        <div>
            <label for="city" class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">Ville</label>
            <input type="text" name="city" id="city" value="{{ request('city') }}" placeholder="Ex : Paris, Lyon..." class="block w-full rounded-lg border-2 border-black bg-white text-black text-sm font-medium focus:border-black focus:ring-2 focus:ring-black placeholder-neutral-400 shadow-sm">
        </div>

        <!-- Filtre Date -->
        <div>
            <label for="date" class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">Date</label>
            <input type="date" name="date" id="date" value="{{ request('date') }}" class="block w-full rounded-lg border-2 border-black bg-white text-black text-sm font-medium focus:border-black focus:ring-2 focus:ring-black shadow-sm">
        </div>

        <!-- Actions -->
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-black text-white hover:bg-neutral-800 font-bold px-4 py-2.5 rounded-lg border-2 border-black transition shadow-sm text-sm uppercase tracking-wide">
                Filtrer
            </button>
            @if(request()->hasAny(['sport', 'city', 'date']))
                <a href="{{ route('events.search') }}" class="px-4 py-2.5 border-2 border-black text-black bg-white hover:bg-black hover:text-white font-bold rounded-lg transition text-center text-sm shadow-sm uppercase tracking-wide">
                    Effacer
                </a>
            @endif
        </div>
    </div>
</form>
