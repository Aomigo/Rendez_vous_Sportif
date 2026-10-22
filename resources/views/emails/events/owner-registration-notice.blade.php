<x-mail::message>
# Nouvelle inscription

**{{ $participant->name }}** ({{ $participant->email }}) s'est inscrit(e) à **« {{ $event->title }} »** du {{ $event->starts_at->format('d/m/Y à H:i') }}.

Places : {{ $event->confirmedParticipantsCount() }} / {{ $event->capacity }} réservées.

<x-mail::button :url="route('events.details', $event->public_token)" color="primary">
Gérer l'événement
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
