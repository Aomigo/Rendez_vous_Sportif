<form method="GET" action="{{ route('events.index') }}" class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Filtre Sport -->
        <div>
            <label for="sport" class="block text-sm font-medium text-gray-700">Sport</label>
            <select name="sport" id="sport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
            <label for="city" class="block text-sm font-medium text-gray-700">Ville</label>
            <input type="text" name="city" id="city" value="{{ request('city') }}" placeholder="Ex : Paris, Lyon..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <!-- Filtre Date -->
        <div>
            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
            <input type="date" name="date" id="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <!-- Actions -->
        <div class="flex items-end gap-2">
            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                Filtrer
            </button>
            @if(request()->hasAny(['sport', 'city', 'date']))
                <a href="{{ route('events.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-center">
                    Effacer
                </a>
            @endif
        </div>
    </div>
</form>