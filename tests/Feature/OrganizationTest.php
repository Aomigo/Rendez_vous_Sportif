<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTest extends TestCase
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

    public function test_user_can_create_several_organizations_and_is_owner_of_each(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/organizations', ['name' => 'Foot du jeudi'])->assertRedirect('/organizations');
        $this->actingAs($user)->post('/organizations', ['name' => 'Foot du jeudi'])->assertRedirect('/organizations');

        $organizations = $user->organizations()->get();
        $this->assertCount(2, $organizations);
        $this->assertTrue($organizations->every(fn ($o) => (bool) $o->pivot->is_owner));
        $this->assertNotEquals($organizations[0]->slug, $organizations[1]->slug);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/organizations', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_owner_can_rename_and_delete(): void
    {
        $owner = User::factory()->create();
        $organization = $this->organizationWith($owner);

        $this->actingAs($owner)->put("/organizations/{$organization->id}", ['name' => 'Nouveau nom'])->assertSessionHasNoErrors();
        $this->assertSame('Nouveau nom', $organization->fresh()->name);

        $this->actingAs($owner)->delete("/organizations/{$organization->id}")->assertRedirect('/organizations');
        $this->assertModelMissing($organization);
        $this->assertDatabaseMissing('organization_user', ['organization_id' => $organization->id]);
    }

    public function test_member_cannot_rename_delete_or_manage_members(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = $this->organizationWith($owner, $member);

        $this->actingAs($member)->get("/organizations/{$organization->id}/edit")->assertForbidden();
        $this->actingAs($member)->put("/organizations/{$organization->id}", ['name' => 'Hack'])->assertForbidden();
        $this->actingAs($member)->delete("/organizations/{$organization->id}")->assertForbidden();
        $this->actingAs($member)->post("/organizations/{$organization->id}/members", ['email' => $owner->email])->assertForbidden();
        $this->actingAs($member)->delete("/organizations/{$organization->id}/members/{$owner->id}")->assertForbidden();

        $this->assertModelExists($organization);
    }

    public function test_owner_can_add_and_remove_member_by_email(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $organization = $this->organizationWith($owner);

        $this->actingAs($owner)->post("/organizations/{$organization->id}/members", ['email' => $other->email])->assertSessionHasNoErrors();
        $this->assertTrue($organization->hasMember($other));
        $this->assertFalse($organization->isOwnedBy($other));

        $this->actingAs($owner)->post("/organizations/{$organization->id}/members", ['email' => $other->email])->assertSessionHasErrors('email');

        $this->actingAs($owner)->delete("/organizations/{$organization->id}/members/{$other->id}")->assertSessionHasNoErrors();
        $this->assertFalse($organization->hasMember($other));
    }

    public function test_unknown_email_and_owner_removal_are_rejected(): void
    {
        $owner = User::factory()->create();
        $organization = $this->organizationWith($owner);

        $this->actingAs($owner)->post("/organizations/{$organization->id}/members", ['email' => 'inconnu@example.com'])->assertSessionHasErrors('email');
        $this->actingAs($owner)->delete("/organizations/{$organization->id}/members/{$owner->id}")->assertSessionHasErrors('member');
        $this->assertTrue($organization->isOwnedBy($owner));
    }

    public function test_member_can_switch_active_organization_kept_in_session(): void
    {
        $user = User::factory()->create();
        $first = $this->organizationWith(User::factory()->create(), $user);
        $second = $this->organizationWith(User::factory()->create(), $user);

        $this->actingAs($user)->put("/organizations/{$second->id}/switch")->assertSessionHas('current_organization_id', $second->id);
        $this->assertTrue($user->currentOrganization()->is($second));

        $this->actingAs($user)->put("/organizations/{$first->id}/switch");
        $this->assertTrue($user->currentOrganization()->is($first));
    }

    public function test_cannot_switch_to_organization_user_is_not_member_of(): void
    {
        $organization = $this->organizationWith(User::factory()->create());

        $this->actingAs(User::factory()->create())->put("/organizations/{$organization->id}/switch")->assertForbidden();
    }

    public function test_index_shows_name_role_and_members_count(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = $this->organizationWith($owner, $member);

        $this->actingAs($member)->get('/organizations')
            ->assertOk()
            ->assertSeeInOrder([$organization->name, 'Membre', '2 membres']);

        $this->actingAs($owner)->get('/organizations')
            ->assertSeeInOrder([$organization->name, 'Owner', '2 membres']);
    }
}
