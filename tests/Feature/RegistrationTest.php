<?php

namespace Tests\Feature;

use App\Events\ParticipantRegistered;
use App\Mail\OwnerRegistrationNoticeMail;
use App\Mail\ParticipantRegistrationMail;
use App\Mail\RegistrationCancelledMail;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function eventOwnedBy(User ...$owners): Event
    {
        $organization = Organization::factory()->create();
        foreach ($owners as $owner) {
            $organization->members()->attach($owner, ['is_owner' => true]);
        }

        return Event::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $owners[0]->id,
            'title' => 'Five du jeudi soir',
            'capacity' => 2,
            'starts_at' => now()->addDays(2),
        ]);
    }

    public function test_joining_confirms_registration_and_queues_mails_to_participant_and_every_owner(): void
    {
        $firstOwner = User::factory()->create();
        $secondOwner = User::factory()->create();
        $member = User::factory()->create();
        $event = $this->eventOwnedBy($firstOwner, $secondOwner);
        $event->organization->members()->attach($member, ['is_owner' => false]);
        $participant = User::factory()->create();
        Mail::fake();

        $response = $this->actingAs($participant)->post(route('events.join', $event->public_token));

        $response->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('eventParticipants', ['event_id' => $event->id, 'user_id' => $participant->id, 'status' => Event::PARTICIPANT_CONFIRMED]);

        Mail::assertQueued(ParticipantRegistrationMail::class, fn (ParticipantRegistrationMail $mail) => $mail->hasTo($participant->email) && $mail->event->is($event));
        Mail::assertQueuedCount(3);
        Mail::assertQueued(OwnerRegistrationNoticeMail::class, fn (OwnerRegistrationNoticeMail $mail) => $mail->hasTo($firstOwner->email) && $mail->participant->is($participant));
        Mail::assertQueued(OwnerRegistrationNoticeMail::class, fn (OwnerRegistrationNoticeMail $mail) => $mail->hasTo($secondOwner->email));
        Mail::assertNotQueued(OwnerRegistrationNoticeMail::class, fn (OwnerRegistrationNoticeMail $mail) => $mail->hasTo($member->email));
        Mail::assertNothingSent();
    }

    public function test_joining_twice_registers_and_dispatches_participant_registered_only_once(): void
    {
        $event = $this->eventOwnedBy(User::factory()->create());
        $participant = User::factory()->create();
        EventFacade::fake([ParticipantRegistered::class]);

        $this->actingAs($participant)->post(route('events.join', $event->public_token));
        $this->actingAs($participant)->post(route('events.join', $event->public_token))
            ->assertSessionHasErrors(['registration' => 'Vous êtes déjà inscrit à cet événement.']);

        $this->assertSame(1, DB::table('eventParticipants')->where('event_id', $event->id)->count());
        EventFacade::assertDispatchedTimes(ParticipantRegistered::class, 1);
    }

    public function test_joining_is_refused_when_event_is_full_cancelled_or_past(): void
    {
        $owner = User::factory()->create();
        $full = $this->eventOwnedBy($owner);
        $full->participants()->attach(User::factory()->count(2)->create(), ['registered_at' => now()]);
        $cancelled = $this->eventOwnedBy($owner);
        $cancelled->update(['status' => Event::STATUS_CANCELLED]);
        $past = $this->eventOwnedBy($owner);
        $past->update(['starts_at' => now()->subDay()]);
        $participant = User::factory()->create();
        Mail::fake();

        $this->actingAs($participant)->post(route('events.join', $full->public_token))
            ->assertSessionHasErrors(['registration' => 'Cet événement est complet.']);
        $this->actingAs($participant)->post(route('events.join', $cancelled->public_token))
            ->assertSessionHasErrors(['registration' => 'Cet événement a été annulé.']);
        $this->actingAs($participant)->post(route('events.join', $past->public_token))
            ->assertSessionHasErrors(['registration' => 'Cet événement est déjà passé.']);

        $this->assertDatabaseMissing('eventParticipants', ['user_id' => $participant->id]);
        Mail::assertNothingQueued();
    }

    public function test_leaving_queues_cancellation_mail_only_when_a_registration_existed(): void
    {
        $event = $this->eventOwnedBy(User::factory()->create());
        $participant = User::factory()->create();
        $stranger = User::factory()->create();
        $event->participants()->attach($participant, ['registered_at' => now()]);
        Mail::fake();

        $this->actingAs($participant)->delete(route('events.leave', $event->public_token))->assertRedirect();
        $this->actingAs($stranger)->delete(route('events.leave', $event->public_token))->assertRedirect();

        $this->assertDatabaseMissing('eventParticipants', ['event_id' => $event->id, 'user_id' => $participant->id]);
        Mail::assertQueuedCount(1);
        Mail::assertQueued(RegistrationCancelledMail::class, fn (RegistrationCancelledMail $mail) => $mail->hasTo($participant->email) && $mail->event->is($event));
    }

    public function test_mails_show_participant_name_and_event(): void
    {
        $event = $this->eventOwnedBy(User::factory()->create());
        $participant = User::factory()->create(['name' => 'Alice Martin', 'email' => 'alice@example.com']);

        (new ParticipantRegistrationMail($event, $participant))
            ->assertSeeInHtml('Alice Martin')
            ->assertSeeInHtml('Five du jeudi soir')
            ->assertHasSubject('Inscription confirmée : Five du jeudi soir');
        (new OwnerRegistrationNoticeMail($event, $participant))
            ->assertSeeInHtml('Alice Martin')
            ->assertSeeInHtml('alice@example.com')
            ->assertSeeInHtml('Five du jeudi soir');
        (new RegistrationCancelledMail($event, $participant))
            ->assertSeeInHtml('Alice Martin')
            ->assertSeeInHtml('a bien été annulée');
    }

    public function test_mail_transport_failure_keeps_the_registration(): void
    {
        config([
            'queue.default' => 'database',
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1,
        ]);
        $event = $this->eventOwnedBy(User::factory()->create());
        $participant = User::factory()->create();

        $this->actingAs($participant)->post(route('events.join', $event->public_token))->assertSessionHasNoErrors();
        $this->artisan('queue:work', ['--stop-when-empty' => true, '--tries' => 1]);

        $this->assertDatabaseHas('eventParticipants', ['event_id' => $event->id, 'user_id' => $participant->id]);
        $this->assertDatabaseCount('failed_jobs', 2);
    }
}
