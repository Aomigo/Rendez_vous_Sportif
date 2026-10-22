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
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('events.details', $event->public_token));
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

    public function test_dashboard_lists_active_organization_events_grouped_with_registration_counts(): void
    {
        $owner = User::factory()->create();
        $organization = $this->organizationWith($owner);
        $sport = Sport::factory()->create(['name' => 'Padel']);
        $events = fn (array $attributes) => Event::factory()->create(['organization_id' => $organization->id, 'sport_id' => $sport->id] + $attributes);

        $upcoming = $events(['title' => 'Match à venir', 'city' => 'Lyon', 'capacity' => 8, 'starts_at' => now()->addDay()]);
        $events(['title' => 'Match terminé', 'starts_at' => now()->subDay()]);
        $events(['title' => 'Match annulé', 'status' => Event::STATUS_CANCELLED]);
        Event::factory()->create(['title' => 'Autre organisation']);

        $upcoming->participants()->attach(User::factory()->count(3)->create(), ['registered_at' => now()]);
        $upcoming->participants()->attach(User::factory()->create(), ['registered_at' => now(), 'status' => 'waitlisted']);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['À venir', 'Match à venir', 'Padel', 'Lyon', '3 / 8', 'Terminés', 'Match terminé', 'Annulés', 'Match annulé'])
            ->assertDontSee('Autre organisation');
    }

    public function test_only_organization_owner_or_admin_can_access_dashboard(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $this->organizationWith($owner, $member);
        $outsider = User::factory()->create();
        $this->organizationWith(User::factory()->create(), $outsider);

        $this->actingAs($owner)->get(route('dashboard'))->assertOk();
        $this->actingAs($member)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($outsider)->get(route('dashboard'))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee("Vous n'avez pas d'organisation active.");
    }

    public function test_event_page_shows_confirmed_participants_only_to_owner_or_admin(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = $this->organizationWith($owner, $member);
        $event = Event::factory()->create(['organization_id' => $organization->id]);

        $confirmed = User::factory()->create(['name' => 'Alice Confirmée', 'email' => 'alice@example.com']);
        $waitlisted = User::factory()->create(['email' => 'bob@example.com']);
        $event->participants()->attach($confirmed, ['registered_at' => '2026-10-20 18:30:00']);
        $event->participants()->attach($waitlisted, ['registered_at' => now(), 'status' => 'waitlisted']);

        foreach ([$owner, User::factory()->create(['is_admin' => true])] as $manager) {
            $this->actingAs($manager)
                ->get(route('events.details', $event->public_token))
                ->assertOk()
                ->assertSee('Participants confirmés')
                ->assertSee('Alice Confirmée')
                ->assertSee('alice@example.com')
                ->assertSee('20/10/2026 18:30')
                ->assertDontSee('bob@example.com')
                ->assertSee(route('events.details', $event->public_token));
        }

        $this->actingAs($member)
            ->get(route('events.details', $event->public_token))
            ->assertOk()
            ->assertDontSee('Participants confirmés')
            ->assertDontSee('alice@example.com');

        auth()->logout();
        $this->get(route('events.details', $event->public_token))
            ->assertOk()
            ->assertDontSee('alice@example.com');
    }

    public function test_search_cards_show_owner_banner_only_to_organization_owner(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = $this->organizationWith($owner, $member);
        Event::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($owner)->get(route('events.search'))->assertSee('Vous êtes organisateur');
        $this->actingAs($member)->get(route('events.search'))->assertDontSee('Vous êtes organisateur');
    }

    public function test_joined_events_are_flagged_on_cards_and_listed_without_management_actions(): void
    {
        $participant = User::factory()->create();
        $joined = Event::factory()->create(['title' => 'Match rejoint', 'starts_at' => now()->addDay()]);
        Event::factory()->create(['title' => 'Match non rejoint', 'starts_at' => now()->addDays(2)]);
        $joined->participants()->attach($participant, ['registered_at' => now()]);

        $this->actingAs($participant)
            ->get(route('events.search'))
            ->assertSeeTextInOrder(['Match rejoint', 'Vous participez à cet événement', 'Match non rejoint']);

        $this->actingAs($participant)
            ->get(route('events.index'))
            ->assertOk()
            ->assertSeeInOrder(['Mes inscriptions', 'Match rejoint', route('events.details', $joined->public_token)])
            ->assertDontSee(route('events.edit', $joined))
            ->assertDontSee('Match non rejoint');
    }
}
