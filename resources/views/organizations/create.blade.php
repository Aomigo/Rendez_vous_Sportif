<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black tracking-tight leading-tight">
            {{ __('Nouvelle organisation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <form method="POST" action="{{ route('organizations.store') }}" class="max-w-xl space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nom')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus maxlength="255" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Créer') }}</x-primary-button>
                        <a href="{{ route('organizations.index') }}" class="text-sm text-neutral-600 underline hover:text-black">{{ __('Annuler') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
