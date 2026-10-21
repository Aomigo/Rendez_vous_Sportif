<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes événements') }}
            </h2>
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('Nouvel événement') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-sm text-green-600">{{ session('status') }}</p>
            @endif

            <div class="bg-white shadow sm:rounded-lg">
                @forelse ($events as $event)
                    <div class="flex items-center justify-between gap-4 p-4 sm:px-8 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $event->title }}
                                <span class="ms-2 text-xs px-2 py-0.5 rounded-full
                                    @if($event->status === 'En cours') bg-yellow-100 text-yellow-800
                                    @elseif($event->status === 'Terminé') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-850 @endif">
                                    {{ $event->status }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ __('Date') }} : {{ \Carbon\Carbon::parse($event->starts_at)->format('d/m/Y H:i') }}
                                · {{ $event->venue }} ({{ $event->city }})
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('events.edit', $event->id) }}" class="text-sm text-gray-600 underline hover:text-gray-900">
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
                    <p class="p-4 sm:px-8 text-gray-600">{{ __("Vous n'avez créé aucun événement.") }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
