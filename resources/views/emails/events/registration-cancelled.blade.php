<x-mail::message>
# Désinscription confirmée

Bonjour {{ $participant->name }},

Votre inscription à **« {{ $event->title }} »** du {{ $event->starts_at->format('d/m/Y à H:i') }} a bien été annulée.

<x-mail::button :url="route('events.details', $event->public_token)" color="primary">
Voir l'événement
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
