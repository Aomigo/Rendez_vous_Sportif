<div>
    <p class="text-sm font-semibold text-black uppercase tracking-wider mb-3">
        {{ __('Participants confirmés') }} ({{ $participants->count() }} / {{ $event->capacity }})
    </p>

    @if ($participants->isEmpty())
        <p class="text-sm text-neutral-600">{{ __('Aucun participant confirmé pour le moment.') }}</p>
    @else
        <div class="overflow-x-auto rounded-lg border-2 border-black">
            <table class="w-full text-sm text-left">
                <thead class="bg-neutral-100 text-xs font-semibold uppercase tracking-wider text-black">
                    <tr>
                        <th class="px-4 py-2">{{ __('Nom') }}</th>
                        <th class="px-4 py-2">{{ __('E-mail') }}</th>
                        <th class="px-4 py-2">{{ __("Date d'inscription") }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/15">
                    @foreach ($participants as $participant)
                        <tr>
                            <td class="px-4 py-2 font-semibold text-black">{{ $participant->name }}</td>
                            <td class="px-4 py-2">{{ $participant->email }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($participant->pivot->registered_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
