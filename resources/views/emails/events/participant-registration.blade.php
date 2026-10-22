<x-mail::message>
# Inscription confirmée

Bonjour {{ $participant->name }},

Votre place est réservée pour :

**{{ $event->title }}** — {{ $event->sport->name ?? 'Sport' }}<br>
📅 {{ $event->starts_at->format('d/m/Y à H:i') }} ({{ $event->duration_minutes }} min)<br>
📍 {{ $event->venue }}, {{ $event->city }}<br>
Organisé par {{ $event->organization->name ?? 'Indépendant' }}

<x-mail::button :url="route('events.details', $event->public_token)" color="primary">
Voir l'événement
</x-mail::button>

Un empêchement ? Vous pouvez vous désinscrire depuis la page de l'événement.

À bientôt,<br>
{{ config('app.name') }}
</x-mail::message>
