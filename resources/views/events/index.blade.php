<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Découvrir les rendez-vous sportifs') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Filtres combinables --}}
        @include('events.filters')

        {{-- Liste des résultats --}}
        @if($events->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    @include('events.card', ['event' => $event])
                @endforeach
            </div>

            {{-- Pagination conservant les filtres --}}
            <div class="mt-8">
                {{ $events->links() }}
            </div>
        @else
            {{-- État vide explicite --}}
            <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <p class="text-gray-500 text-base">Aucun événement à venir ne correspond à vos critères de recherche.</p>
                @if(request()->hasAny(['sport', 'city', 'date']))
                    <div class="mt-4">
                        <a href="{{ route('events.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                            Réinitialiser tous les filtres
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
