<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6 text-gray-900">

                <!-- Header Info -->
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            @if($event->status === 'En cours') bg-yellow-100 text-yellow-800
                            @elseif($event->status === 'Terminé') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $event->status }}
                        </span>
                        <h1 class="text-2xl font-bold mt-2">{{ $event->title }}</h1>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">{{ __('Date et heure') }}</p>
                        <p class="text-base text-gray-800">{{ \Carbon\Carbon::parse($event->starts_at)->format('d/m/Y à H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-500">{{ __('Durée') }}</p>
                        <p class="text-base text-gray-800">{{ $event->duration_minutes }} minutes</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-500">{{ __('Lieu') }}</p>
                        <p class="text-base text-gray-800">{{ $event->venue }}, {{ $event->city }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-500">{{ __('Capacité') }}</p>
                        <p class="text-base text-gray-800">{{ $event->capacity }} personnes</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-500 mb-1">{{ __('Description') }}</p>
                    <p class="text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
                </div>

                <!-- Registration / Participation Section -->
                @auth
                    <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ __('Participation') }}</p>
                            <p class="text-xs text-gray-500">
                                @if($event->created_by === auth()->id())
                                    {{ __('Vous êtes le propriétaire de cet événement.') }}
                                @elseif($event->Event)
                                    {{ __('Vous participez actuellement à cet événement.') }}
                                @else
                                    {{ __('Rejoignez cet événement pour y participer.') }}
                                @endif
                            </p>
                        </div>

                        <div>
                            @if($event->created_by === auth()->id())
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-2 rounded-md">
                                    {{ __('Organisateur') }}
                                </span>
                            @elseif($eventParticipants->contains('user_id', auth()->id()))
                                <!-- Leave Event Button -->
                                <form method="POST" action="{{ route('events.leave', $event->public_token) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">
                                        {{ __('Quitter l\'événement') }}
                                    </x-danger-button>
                                </form>
                            @else
                                <!-- Sign Up Button -->
                                <form method="POST" action="{{ route('events.join', $event->public_token) }}">
                                    @csrf
                                    <x-primary-button type="submit">
                                        {{ __('S\'inscrire') }}
                                    </x-primary-button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-100 pt-6 text-center bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-600">
                            <a href="{{ route('login') }}" class="text-indigo-600 underline font-semibold hover:text-indigo-900">{{ __('Connectez-vous') }}</a>
                            {{ __('pour vous inscrire à cet événement.') }}
                        </p>
                    </div>
                @endauth

                <!-- Share Link Section (Using Alpine.js) -->
                <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4"
                     x-data="{ copied: false }">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ __('Partager cet événement') }}</p>
                        <p class="text-xs text-gray-500">{{ __('Envoyez ce lien à vos amis pour qu\'ils puissent voir les détails.') }}</p>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="text" readonly
                               value="{{ route('events.details', $event->public_token) }}"
                               class="text-xs bg-gray-50 border border-gray-300 rounded px-3 py-2 w-full sm:w-72 text-gray-600 select-all" />

                        <x-secondary-button
                            @click="navigator.clipboard.writeText('{{ route('events.details', $event->public_token) }}'); copied = true; setTimeout(() => copied = false, 2000)">
                            <span x-text="copied ? '{{ __('Copié !') }}' : '{{ __('Copier') }}'"></span>
                        </x-secondary-button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
