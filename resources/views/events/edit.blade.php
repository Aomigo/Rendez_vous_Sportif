<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-black tracking-tight leading-tight">
                    {{ $event->title }}
                </h2>
                <p class="text-sm text-neutral-600">
                    {{ __('Statut') }} : {{ $event->isCancelled() ? __('Annulé') : __('Publié') }}
                    · {{ trans_choice(':count inscrit confirmé|:count inscrits confirmés', $event->confirmedParticipantsCount) }}
                    · {{ __('Token public') }} : <code class="text-xs bg-neutral-100 border border-black px-1 py-0.5 rounded">{{ $event->publicToken }}</code>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-sm text-green-600">{{ session('status') }}</p>
            @endif

            <div class="p-4 sm:p-8 bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <form method="POST" action="{{ route('events.update', $event->id) }}" class="max-w-xl space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="sport_id" :value="__('Sport')" />
                        <select id="sport_id" name="sport_id" required class="mt-1 block w-full rounded-lg border-2 border-black bg-white text-black text-sm font-medium focus:border-black focus:ring-2 focus:ring-black">
                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}" @selected(old('sport_id', $event->sportId) == $sport->id)>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('sport_id')" />
                    </div>

                    <div>
                        <x-input-label for="title" :value="__('Titre')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $event->title)" required maxlength="255" />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="4" required class="mt-1 block w-full rounded-lg border-2 border-black bg-white text-black text-sm font-medium focus:border-black focus:ring-2 focus:ring-black">{{ old('description', $event->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="starts_at" :value="__('Date et heure')" />
                            <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full" :value="old('starts_at', $event->startsAt->format('Y-m-d\TH:i'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('starts_at')" />
                        </div>

                        <div>
                            <x-input-label for="duration_minutes" :value="__('Durée (minutes)')" />
                            <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="30" max="240" class="mt-1 block w-full" :value="old('duration_minutes', $event->durationMinutes)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="city" :value="__('Ville')" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $event->city)" required maxlength="255" />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <div>
                            <x-input-label for="venue" :value="__('Lieu')" />
                            <x-text-input id="venue" name="venue" type="text" class="mt-1 block w-full" :value="old('venue', $event->venue)" required maxlength="255" />
                            <x-input-error class="mt-2" :messages="$errors->get('venue')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="capacity" :value="__('Capacité (2 à 100 participants)')" />
                        <x-text-input id="capacity" name="capacity" type="number" min="2" max="100" class="mt-1 block w-full" :value="old('capacity', $event->capacity)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                    </div>

                    <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                </form>
            </div>

            @if (! $event->isCancelled())
                <div class="p-4 sm:p-8 bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div class="max-w-xl space-y-4">
                        <h3 class="text-lg font-bold text-black tracking-tight">{{ __("Annuler l'événement") }}</h3>
                        <p class="text-sm text-neutral-600">
                            {{ __("L'annulation de l'événement conservera les inscriptions existantes pour l'historique.") }}
                        </p>
                        <form method="POST" action="{{ route('events.cancel', $event->id) }}"
                              onsubmit="return confirm('{{ __('Annuler cet événement ?') }}')">
                            @csrf
                            @method('patch')
                            <x-danger-button>{{ __("Annuler l'événement") }}</x-danger-button>
                        </form>
                    </div>
                </div>
            @endif

            <a href="{{ route('events.details', $event->publicToken) }}" class="inline-block text-sm text-neutral-600 underline hover:text-black me-4">{{ __("Retour à l'événement") }}</a>
            <a href="{{ route('organizations.index') }}" class="inline-block text-sm text-neutral-600 underline hover:text-black">{{ __('Retour aux organisations') }}</a>
        </div>
    </div>
</x-app-layout>

