<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-black tracking-tight leading-tight">
                {{ __('Mes événements') }}
            </h2>
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-black border-2 border-black rounded-lg font-bold text-xs text-white uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 transition-all duration-150">
                {{ __('Nouvel événement') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-sm text-green-600">{{ session('status') }}</p>
            @endif

            <div class="bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                @forelse ($events as $event)
                    <div class="flex items-center justify-between gap-4 p-4 sm:px-8 border-b border-black/15 last:border-0">
                        <div>
                            <p class="font-medium text-black">
                                {{ $event->title }}
                                <span class="ms-2 text-xs px-2 py-0.5 rounded-full
                                    @if($event->status === 'En cours') bg-yellow-100 text-yellow-800
                                    @elseif($event->status === 'Terminé') bg-green-100 text-green-800
                                    @else bg-neutral-100 text-black @endif">
                                    {{ $event->status }}
                                </span>
                            </p>
                            <p class="text-sm text-neutral-600 mt-1">
                                {{ __('Date') }} : {{ \Carbon\Carbon::parse($event->starts_at)->format('d/m/Y H:i') }}
                                · {{ $event->venue }} ({{ $event->city }})
                            </p>
                        </div>


                        <div class="flex items-center gap-3">
                            <a href="{{ route('events.details', $event->public_token) }}" class="inline-flex items-center px-4 py-2 bg-black border-2 border-black rounded-lg font-bold text-xs text-white uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 transition-all duration-150">
                                {{ __('Infos') }}
                            </a>
                            <a href="{{ route('events.edit', $event->id) }}" class="text-sm text-neutral-600 underline hover:text-black">
                                {{ __('Gérer') }}
                            </a>

                            <form method="POST" action="{{ route('events.destroy', $event->id) }}"
                                  onsubmit="return confirm('{{ __('Supprimer cet événement ?') }}')">
                                @csrf
                                @method('delete')
                                <x-danger-button>{{ __('Supprimer') }}</x-danger-button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-4 sm:px-8 text-neutral-600">{{ __("Vous n'avez créé aucun événement.") }}</p>
                @endforelse
            </div>

            <h3 class="pt-4 text-lg font-bold text-black tracking-tight">{{ __('Mes inscriptions') }}</h3>

            <div class="bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                @forelse ($joinedEvents as $event)
                    <div class="flex items-center justify-between gap-4 p-4 sm:px-8 border-b border-black/15 last:border-0">
                        <div>
                            <p class="font-medium text-black">
                                {{ $event->title }}
                                <span class="ms-2 text-xs px-2 py-0.5 rounded-full border border-black bg-neutral-100 text-black">
                                    {{ $event->statusLabel() }}
                                </span>
                            </p>
                            <p class="text-sm text-neutral-600 mt-1">
                                {{ __('Date') }} : {{ $event->starts_at->format('d/m/Y H:i') }}
                                · {{ $event->venue }} ({{ $event->city }})
                            </p>
                        </div>

                        <a href="{{ route('events.details', $event->public_token) }}" class="inline-flex items-center px-4 py-2 bg-black border-2 border-black rounded-lg font-bold text-xs text-white uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 transition-all duration-150">
                            {{ __('Infos') }}
                        </a>
                    </div>
                @empty
                    <p class="p-4 sm:px-8 text-neutral-600">{{ __("Vous n'êtes inscrit à aucun événement.") }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
