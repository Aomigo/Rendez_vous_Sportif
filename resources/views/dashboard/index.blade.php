<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black tracking-tight leading-tight">
            {{ __('Dashboard') }}@if ($organization) · {{ $organization->name }}@endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if ($organization === null)
                <div class="bg-white p-6 rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-black font-semibold">
                    {{ __("Vous n'avez pas d'organisation active.") }}
                </div>
            @endif

            @foreach ($eventGroups as $label => $events)
                <section class="bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                    <h3 class="px-6 py-4 text-lg font-bold text-black tracking-tight border-b-2 border-black">
                        {{ $label }} <span class="text-sm font-semibold text-neutral-500">({{ $events->count() }})</span>
                    </h3>

                    @if ($events->isEmpty())
                        <p class="px-6 py-4 text-sm text-neutral-600">{{ __('Aucun événement.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-neutral-100 text-xs font-semibold uppercase tracking-wider text-black">
                                    <tr>
                                        <th class="px-6 py-3">{{ __('Événement') }}</th>
                                        <th class="px-6 py-3">{{ __('Sport') }}</th>
                                        <th class="px-6 py-3">{{ __('Date') }}</th>
                                        <th class="px-6 py-3">{{ __('Ville') }}</th>
                                        <th class="px-6 py-3">{{ __('Statut') }}</th>
                                        <th class="px-6 py-3">{{ __('Inscrits') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/15">
                                    @foreach ($events as $event)
                                        <tr>
                                            <td class="px-6 py-3 font-semibold text-black">
                                                <a href="{{ route('events.details', $event->public_token) }}" class="underline hover:no-underline">{{ $event->title }}</a>
                                            </td>
                                            <td class="px-6 py-3">{{ $event->sport->name ?? '—' }}</td>
                                            <td class="px-6 py-3">{{ $event->starts_at->translatedFormat('d/m/Y H:i') }}</td>
                                            <td class="px-6 py-3">{{ $event->city }}</td>
                                            <td class="px-6 py-3">{{ $event->statusLabel() }}</td>
                                            <td class="px-6 py-3 font-semibold">{{ $event->confirmed_participants_count }} / {{ $event->capacity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
</x-app-layout>
