<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes organisations') }}
            </h2>
            <a href="{{ route('organizations.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('Nouvelle organisation') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-sm text-green-600">{{ session('status') }}</p>
            @endif

            <div class="bg-white shadow sm:rounded-lg">
                @forelse ($organizations as $organization)
                    <div class="flex items-center justify-between gap-4 p-4 sm:px-8 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $organization->name }}
                                @if ($organization->id === $currentOrganizationId)
                                    <span class="ms-2 text-xs text-indigo-600">{{ __('active') }}</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ __('Rôle') }} : {{ $organization->role() }}
                                · {{ trans_choice(':count membre|:count membres', $organization->membersCount) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($organization->id !== $currentOrganizationId)
                                <form method="POST" action="{{ route('organizations.switch', $organization->id) }}">
                                    @csrf
                                    @method('put')
                                    <x-secondary-button type="submit">{{ __('Activer') }}</x-secondary-button>
                                </form>
                            @endif

                            @if ($organization->isOwner)
                                <a href="{{ route('organizations.edit', $organization->id) }}" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    {{ __('Gérer') }}
                                </a>

                                <form method="POST" action="{{ route('organizations.destroy', $organization->id) }}"
                                      onsubmit="return confirm('{{ __('Supprimer cette organisation ?') }}')">
                                    @csrf
                                    @method('delete')
                                    <x-danger-button>{{ __('Supprimer') }}</x-danger-button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="p-4 sm:px-8 text-gray-600">{{ __("Vous n'êtes membre d'aucune organisation.") }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
