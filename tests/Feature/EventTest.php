<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    private function organizationWith(User $owner, User ...$members): Organization
    {
        $organization = Organization::factory()->create();
        $organization->members()->attach($owner, ['is_owner' => true]);
        foreach ($members as $member) {
            $organization->members()->attach($member, ['is_owner' => false]);
        }

        return $organization;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(?Sport $sport = null, array $overrides = []): array
    {
        $sport ??= Sport::factory()->create();

        return array_merge([
            'sport_id' => $sport->id,
            'title' => 'Match du jeudi soir',
            'description' => 'Five convivial ouvert à tous les niveaux.',
            'starts_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'duration_minutes' => 90,
            'city' => 'Paris',
            'venue' => 'Gymnase Jaurès',
            'capacity' => 10,
        ], $overrides);
    }

    public function test_owner_can_create_published_event_with_unguessable_public_token(): void
    {
        $owner = User::factory()->create();
        $organization = $this->organizationWith($owner);
        $sport = Sport::factory()->create();

        $this->actingAs($owner)
            ->get('/events/create')
            ->assertOk()
            ->assertSee($sport->name);

        $response = $this->actingAs($owner)
            ->post('/events', $this->validPayload($sport));

        $event = Event::firstOrFail();

        $response->assertRedirect(route('events.edit', $event));
        $this->assertSame($organization->id, $event->organization_id);
        $this->assertSame($sport->id, $event->sport_id);
        $this->assertSame($owner->id, $event->created_by);
        $this->assertSame('Match du jeudi soir', $event->title);
        $this->assertSame(90, $event->duration_minutes);
        $this->assertSame(10, $event->capacity);
        $this->assertSame(Event::STATUS_PUBLISHED, $event->status);
        $this->assertNotEmpty($event->public_token);
        $this->assertGreaterThanOrEqual(32, strlen($event->public_token));
    }

    public function test_validation_rejects_missing_or_out_of_bounds_fields(): void
    {
        $owner = User::factory()->create();
        $this->organizationWith($owner);
        $sport = Sport::factory()->create();

        // Champs obligatoires manquants
        $this->actingAs($owner)
            ->post('/events', [])
            ->assertSessionHasErrors([
                'sport_id',
                'title',
                'description',
                'starts_at',
                'duration_minutes',
                'city',
                'venue',
                'capacity',
            ]);

        // Date passée, durée hors [30, 240], capacité hors [2, 100]
        $this->actingAs($owner)
            ->post('/events', $this->validPayload($sport, [
                'starts_at' => now()->subDay()->format('Y-m-d H:i:s'),
                'duration_minutes' => 20,
                'capacity' => 1,
            ]))
            ->assertSessionHasErrors(['starts_at', 'duration_minutes', 'capacity']);

        $this->actingAs($owner)
            ->post('/events', $this->validPayload($sport, [
                'duration_minutes' => 245,
                'capacity' => 101,
            ]))
            ->assertSessionHasErrors(['duration_minutes', 'capacity']);
    }

    public function test_only_organization_owner_or_admin_can_create_update_or_cancel_events(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $otherOwner = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $organization = $this->organizationWith($owner, $member);
        $this->organizationWith($otherOwner);
        $sport = Sport::factory()->create();

        // Un simple membre ne peut pas créer d'événement
        $this->actingAs($member)->get('/events/create')->assertForbidden();
        $this->actingAs($member)->post('/events', $this->validPayload($sport))->assertForbidden();

        // Le owner crée l'événement
        $this->actingAs($owner)->post('/events', $this->validPayload($sport))->assertSessionHasNoErrors();
        $event = Event::firstOrFail();

        // Un membre ou le owner d'une autre organisation ne peut ni modifier ni annuler
        $this->actingAs($member)->get("/events/{$event->id}/edit")->assertForbidden();
        $this->actingAs($member)->put("/events/{$event->id}", $this->validPayload($sport, ['title' => 'Hack']))->assertForbidden();
        $this->actingAs($member)->patch("/events/{$event->id}/cancel")->assertForbidden();

        $this->actingAs($otherOwner)->get("/events/{$event->id}/edit")->assertForbidden();
        $this->actingAs($otherOwner)->put("/events/{$event->id}", $this->validPayload($sport, ['title' => 'Hack']))->assertForbidden();
        $this->actingAs($otherOwner)->patch("/events/{$event->id}/cancel")->assertForbidden();

        // Le owner de l'organisation peut modifier son événement
        $this->actingAs($owner)
            ->put("/events/{$event->id}", $this->validPayload($sport, ['title' => 'Titre modifié par owner']))
            ->assertSessionHasNoErrors();
        $this->assertSame('Titre modifié par owner', $event->fresh()->title);

        // Un admin peut modifier et annuler n'importe quel événement
        $this->actingAs($admin)
            ->get("/events/{$event->id}/edit")
            ->assertOk();

        $this->actingAs($admin)
            ->put("/events/{$event->id}", $this->validPayload($sport, ['title' => 'Titre modifié par admin']))
            ->assertSessionHasNoErrors();
        $this->assertSame('Titre modifié par admin', $event->fresh()->title);

        $this->actingAs($admin)
            ->patch("/events/{$event->id}/cancel")
            ->assertSessionHasNoErrors();
        $this->assertSame(Event::STATUS_CANCELLED, $event->fresh()->status);
    }

    public function test_cancelling_event_preserves_registrations_for_history(): void
    {
        $owner = User::factory()->create();
        $participant1 = User::factory()->create();
        $participant2 = User::factory()->create();
        $this->organizationWith($owner);
        $sport = Sport::factory()->create();

        $this->actingAs($owner)->post('/events', $this->validPayload($sport));
        $event = Event::firstOrFail();

        DB::table('eventParticipants')->insert([
            [
                'event_id' => $event->id,
                'user_id' => $participant1->id,
                'status' => Event::PARTICIPANT_CONFIRMED,
                'registered_at' => now(),
            ],
            [
                'event_id' => $event->id,
                'user_id' => $participant2->id,
                'status' => 'waitlisted',
                'registered_at' => now(),
            ],
        ]);

        $this->actingAs($owner)
            ->patch("/events/{$event->id}/cancel")
            ->assertRedirect(route('events.edit', $event));

        $this->assertSame(Event::STATUS_CANCELLED, $event->fresh()->status);
        $this->assertDatabaseCount('eventParticipants', 2);
        $this->assertDatabaseHas('eventParticipants', [
            'event_id' => $event->id,
            'user_id' => $participant1->id,
            'status' => Event::PARTICIPANT_CONFIRMED,
        ]);
    }

    public function test_update_fails_if_new_capacity_is_below_confirmed_registrations_count(): void
    {
        $owner = User::factory()->create();
        $this->organizationWith($owner);
        $sport = Sport::factory()->create();

        $this->actingAs($owner)->post('/events', $this->validPayload($sport, ['capacity' => 10]));
        $event = Event::firstOrFail();

        // 4 inscriptions confirmées et 2 en liste d'attente
        foreach (User::factory()->count(4)->create() as $user) {
            DB::table('eventParticipants')->insert([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => Event::PARTICIPANT_CONFIRMED,
                'registered_at' => now(),
            ]);
        }
        foreach (User::factory()->count(2)->create() as $user) {
            DB::table('eventParticipants')->insert([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'waitlisted',
                'registered_at' => now(),
            ]);
        }

        // Capacité 3 (< 4 confirmés) -> rejetée
        $this->actingAs($owner)
            ->put("/events/{$event->id}", $this->validPayload($sport, ['capacity' => 3]))
            ->assertSessionHasErrors('capacity');
        $this->assertSame(10, $event->fresh()->capacity);

        // Capacité 4 (== 4 confirmés) -> acceptée
        $this->actingAs($owner)
            ->put("/events/{$event->id}", $this->validPayload($sport, ['capacity' => 4]))
            ->assertSessionHasNoErrors();
        $this->assertSame(4, $event->fresh()->capacity);
    }

    public function test_can_render_events_index_page(): void
    {
        $sport = Sport::factory()->create(['name' => 'Basketball', 'active' => true]);
        $owner = User::factory()->create();
        $organization = $this->organizationWith($owner);

        $event = Event::factory()->create([
            'organization_id' => $organization->id,
            'sport_id' => $sport->id,
            'created_by' => $owner->id,
            'title' => 'Tournoi de Basket 3v3',
            'city' => 'Nantes',
            'starts_at' => now()->addDays(2),
            'capacity' => 12,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $this->get(route('events.search'))
            ->assertOk()
            ->assertSee('Découvrir les rendez-vous sportifs')
            ->assertSee('Tournoi de Basket 3v3')
            ->assertSee('Nantes')
            ->assertSee('Basketball');
    }
}
