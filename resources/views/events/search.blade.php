<x-app-layout>
    <div class="max-w-7xl py-7 flex-col items-center justify-center mx-auto">
    <div name="header" class=" sm:px-6 lg:px-8 items-center  bg-transparent rounded-xl ">

        <div class="flex m-2 relative py-10 bg-black rounded-xl w-full justify-between px-10 items-center gap-4">
        <h2 class="font-black text-3xl text-white uppercase tracking-tight">
            {{ __('Découvrir les rendez-vous sportifs') }}
        </h2>
        <img class="absolute  w-[200px]   right-3" src="https://cdn3d.iconscout.com/3d/premium/thumb/smash-3d-icon-png-download-8383237.png" alt="">
        </div>
</div>

    <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            <div class="mt-10">
                {{ $events->links() }}
            </div>
        @else
            {{-- État vide explicite --}}
            <div class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <p class="text-black font-semibold text-lg">Aucun événement à venir ne correspond à vos critères de recherche.</p>
                @if(request()->hasAny(['sport', 'city', 'date']))
                    <div class="mt-5">
                        <a href="{{ route('events.search') }}" class="inline-block px-5 py-2.5 bg-black text-white hover:bg-neutral-800 border-2 border-black font-bold text-xs uppercase tracking-wider rounded-lg transition">
                            Réinitialiser tous les filtres
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
    </div>
</x-app-layout>