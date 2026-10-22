<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_events_are_created_by_their_organization_owner_who_is_not_a_participant(): void
    {
        $this->seed();

        foreach (Event::with('organization', 'creator')->get() as $event) {
            $this->assertTrue($event->organization->isOwnedBy($event->creator), "{$event->title} n'est pas créé par l'owner de son organisation.");
            $this->assertFalse($event->participants()->whereKey($event->created_by)->exists(), "L'owner participe à {$event->title}.");
        }
    }

    public function test_seeding_twice_does_not_duplicate_data(): void
    {
        $this->seed();
        $counts = [User::count(), Event::count(), DB::table('organization_user')->count(), DB::table('eventParticipants')->count()];

        $this->seed();

        $this->assertSame($counts, [User::count(), Event::count(), DB::table('organization_user')->count(), DB::table('eventParticipants')->count()]);
        $this->assertSame(3, Organization::count());
        $this->assertSame(3, DB::table('organization_user')->where('is_owner', true)->count());
    }
}
