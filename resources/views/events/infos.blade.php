<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black tracking-tight leading-tight">
            {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-sm text-green-600">{{ session('status') }}</p>
            @endif
            @error('registration')
                <p class="text-sm font-semibold text-red-600">{{ $message }}</p>
            @enderror

            <div class="bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-6 space-y-6 text-black">

                <!-- Header Info -->
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            @if($event->isCancelled()) bg-red-100 text-red-800
                            @elseif($event->isPast()) bg-green-100 text-green-800
                            @else bg-neutral-100 text-black @endif">
                            {{ $event->statusLabel() }}
                        </span>
                        <h1 class="text-2xl font-semibold mt-2">{{ $event->title }}</h1>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-black/15 pt-4">
                    <div>
                        <p class="text-sm font-semibold text-neutral-500">{{ __('Date et heure') }}</p>
                        <p class="text-base text-black">{{ \Carbon\Carbon::parse($event->starts_at)->format('d/m/Y à H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-neutral-500">{{ __('Durée') }}</p>
                        <p class="text-base text-black">{{ $event->duration_minutes }} minutes</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-neutral-500">{{ __('Lieu') }}</p>
                        <p class="text-base text-black">{{ $event->venue }}, {{ $event->city }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-neutral-500">{{ __('Capacité') }}</p>
                        <p class="text-base text-black">{{ $event->capacity }} personnes</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="border-t border-black/15 pt-4">
                    <p class="text-sm font-semibold text-neutral-500 mb-1">{{ __('Description') }}</p>
                    <p class="text-neutral-700 whitespace-pre-line">{{ $event->description }}</p>
                </div>

                <!-- Registration / Participation Section -->
                @auth
                    <div class="border-t border-black/15 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-black">{{ __('Participation') }}</p>
                            <p class="text-xs text-neutral-500">
                                @if($event->created_by === auth()->id())
                                    {{ __('Vous êtes le propriétaire de cet événement.') }}
                                @elseif($isRegistered)
                                    {{ __('Vous participez actuellement à cet événement.') }}
                                @else
                                    {{ __('Rejoignez cet événement pour y participer.') }}
                                @endif
                            </p>
                        </div>

                        <div>
                            @if($event->created_by === auth()->id())
                                <span class="text-xs font-semibold text-black border border-black bg-neutral-100 px-3 py-1 rounded-full">
                                    {{ __('Organisateur') }}
                                </span>
                            @elseif($isRegistered)
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
                    <div class="border-t border-black/15 pt-6 text-center bg-neutral-100 p-4 rounded-lg">
                        <p class="text-sm text-neutral-600">
                            <a href="{{ route('login') }}" class="text-black underline font-semibold hover:text-neutral-600">{{ __('Connectez-vous') }}</a>
                            {{ __('pour vous inscrire à cet événement.') }}
                        </p>
                    </div>
                @endauth

                @if ($canManage)
                    @include('events.manage')
                @endif

                <!-- Share Link Section (Using Alpine.js) -->
                <div class="border-t border-black/15 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4"
                     x-data="{ copied: false }">
                    <div>
                        <p class="text-sm font-medium text-black">{{ __('Partager cet événement') }}</p>
                        <p class="text-xs text-neutral-500">{{ __('Envoyez ce lien à vos amis pour qu\'ils puissent voir les détails.') }}</p>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="text" readonly
                               value="{{ route('events.details', $event->public_token) }}"
                               class="text-xs bg-neutral-100 border-2 border-black rounded-lg px-3 py-2 w-full sm:w-72 text-black select-all" />

                        <button type="button"
                            class="inline-flex items-center px-4 py-2 bg-black border-2 border-black rounded-lg font-bold text-xs text-white uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 transition-all duration-150"
                            @click="navigator.clipboard.writeText('{{ route('events.details', $event->public_token) }}'); copied = true; setTimeout(() => copied = false, 2000)">
                            <span x-text="copied ? '{{ __('Copié !') }}' : '{{ __('Copier') }}'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
