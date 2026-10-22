<?php

namespace Tests\Feature;

use App\Actions\Event\EventClosed;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event as EventFacade;
use Tests\TestCase;

class CloseStartedEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_closes_started_events_and_dispatches_events(): void
    {
        // 1. Isoler le dispatch de l'événement EventClosed
        EventFacade::fake([EventClosed::class]);

        // 2. Préparer les différents scénarios
        // Doit être fermé
        $eventToClose = Event::factory()->create([
            'starts_at' => now()->subMinutes(15),
            'status' => Event::STATUS_PUBLISHED,
        ]);

        // Déjà fermé : ne doit pas être re-traité
        $alreadyClosedEvent = Event::factory()->create([
            'starts_at' => now()->subHour(),
            'status' => Event::STATUS_CLOSED,
        ]);

        // Futur : ne doit pas être fermé
        $futureEvent = Event::factory()->create([
            'starts_at' => now()->addHour(),
            'status' => Event::STATUS_PUBLISHED,
        ]);

        // 3. Exécuter la commande et vérifier le code retour + la sortie texte
        $this->artisan('events:close-started')
            ->expectsOutput('1 event(s) closed.')
            ->assertSuccessful();

        // 4. Vérifications en base de données
        $this->assertDatabaseHas('events', [
            'id' => $eventToClose->id,
            'status' => Event::STATUS_CLOSED,
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $futureEvent->id,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        // 5. Vérifier que EventClosed a été déclenché UNIQUEMENT pour le bon événement
        EventFacade::assertDispatched(EventClosed::class, function (EventClosed $e) use ($eventToClose) {
            return $e->event->id === $eventToClose->id;
        });

        EventFacade::assertNotDispatched(EventClosed::class, function (EventClosed $e) use ($futureEvent) {
            return $e->event->id === $futureEvent->id;
        });

        EventFacade::assertNotDispatched(EventClosed::class, function (EventClosed $e) use ($alreadyClosedEvent) {
            return $e->event->id === $alreadyClosedEvent->id;
        });
    }
}