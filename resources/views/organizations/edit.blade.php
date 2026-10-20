<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $organization->name }}
        </h2>
        <p class="text-sm text-gray-600">
            {{ __('Rôle') }} : {{ $organization->role() }}
            · {{ trans_choice(':count membre|:count membres', $organization->membersCount) }}
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-sm text-green-600">{{ session('status') }}</p>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="POST" action="{{ route('organizations.update', $organization->id) }}" class="max-w-xl space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="name" :value="__('Nom')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $organization->name)" required maxlength="255" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                </form>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @include('organizations.partials.member-list')
            </div>

            <a href="{{ route('organizations.index') }}" class="inline-block text-sm text-gray-600 underline hover:text-gray-900">{{ __('Retour aux organisations') }}</a>
        </div>
    </div>
</x-app-layout>
