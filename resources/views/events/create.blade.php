<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouvel événement sportif') }}
        </h2>
        @if ($organization)
            <p class="text-sm text-gray-600">
                {{ __('Organisation') }} : {{ $organization->name }}
            </p>
        @endif
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="POST" action="{{ route('events.store') }}" class="max-w-xl space-y-6">
                    @csrf

                    @if ($organization)
                        <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                    @endif

                    <div>
                        <x-input-label for="sport_id" :value="__('Sport')" />
                        <select id="sport_id" name="sport_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Sélectionner un sport') }}</option>
                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}" @selected(old('sport_id') == $sport->id)>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('sport_id')" />
                    </div>

                    <div>
                        <x-input-label for="title" :value="__('Titre')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus maxlength="255" />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="4" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="starts_at" :value="__('Date et heure')" />
                            <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full" :value="old('starts_at')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('starts_at')" />
                        </div>

                        <div>
                            <x-input-label for="duration_minutes" :value="__('Durée (minutes)')" />
                            <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="30" max="240" class="mt-1 block w-full" :value="old('duration_minutes', 60)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="city" :value="__('Ville')" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" required maxlength="255" />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <div>
                            <x-input-label for="venue" :value="__('Lieu')" />
                            <x-text-input id="venue" name="venue" type="text" class="mt-1 block w-full" :value="old('venue')" required maxlength="255" />
                            <x-input-error class="mt-2" :messages="$errors->get('venue')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="capacity" :value="__('Capacité (2 à 100 participants)')" />
                        <x-text-input id="capacity" name="capacity" type="number" min="2" max="100" class="mt-1 block w-full" :value="old('capacity', 10)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Publier') }}</x-primary-button>
                        <a href="{{ route('organizations.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">{{ __('Annuler') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

