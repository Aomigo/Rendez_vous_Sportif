<?php

namespace Tests\Feature;

use App\DTOs\Report\DailyEventReportDTO;
use App\Mail\DailyEventReportMail;
use App\Models\Event;
use App\Models\EventParticipants;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendDailyEventReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_daily_report_when_event_has_at_least_11_confirmed_registrations_yesterday(): void
    {
        Mail::fake();

        $owner = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'owner@example.com',
        ]);

        $event = Event::factory()->create([
            'created_by' => $owner->id,
            'title' => 'Tournoi de Basketball',
        ]);

        $yesterday = CarbonImmutable::yesterday(config('app.timezone', 'UTC'))->setTime(14, 0);

        // 11 confirmed registrations yesterday
        for ($i = 0; $i < 11; $i++) {
            $user = User::factory()->create();
            EventParticipants::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'confirmed',
                'registered_at' => $yesterday,
            ]);
        }

        $this->artisan('events:send-daily-reports')
            ->expectsOutput('Traitement terminé : 1 rapport(s) envoyé(s).')
            ->assertSuccessful();

        Mail::assertQueued(DailyEventReportMail::class, function (DailyEventReportMail $mail) use ($owner, $event) {
            return $mail->hasTo('owner@example.com')
                && $mail->report->eventId === $event->id
                && $mail->report->eventName === 'Tournoi de Basketball'
                && $mail->report->registrationsCount === 11
                && $mail->report->ownerEmail === $owner->email;
        });
    }

    public function test_does_not_send_report_when_less_than_11_registrations_yesterday(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $event = Event::factory()->create(['created_by' => $owner->id]);
        $yesterday = CarbonImmutable::yesterday(config('app.timezone', 'UTC'))->setTime(12, 0);

        // 10 confirmed registrations (threshold is > 10, i.e. >= 11)
        for ($i = 0; $i < 10; $i++) {
            $user = User::factory()->create();
            EventParticipants::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'confirmed',
                'registered_at' => $yesterday,
            ]);
        }

        $this->artisan('events:send-daily-reports')
            ->expectsOutput('Traitement terminé : 0 rapport(s) envoyé(s).')
            ->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_does_not_send_duplicate_report_due_to_cache_idempotency(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['email' => 'owner@example.com']);
        $event = Event::factory()->create(['created_by' => $owner->id]);
        $yesterday = CarbonImmutable::yesterday(config('app.timezone', 'UTC'))->setTime(15, 0);

        for ($i = 0; $i < 12; $i++) {
            $user = User::factory()->create();
            EventParticipants::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'confirmed',
                'registered_at' => $yesterday,
            ]);
        }

        // First run
        $this->artisan('events:send-daily-reports')
            ->expectsOutput('Traitement terminé : 1 rapport(s) envoyé(s).')
            ->assertSuccessful();

        Mail::assertQueuedCount(1);

        // Second run on the same day
        $this->artisan('events:send-daily-reports')
            ->expectsOutputToContain("Rapport déjà envoyé pour l'événement #{$event->id}")
            ->expectsOutput('Traitement terminé : 0 rapport(s) envoyé(s).')
            ->assertSuccessful();

        Mail::assertQueuedCount(1);
    }

    public function test_mail_content_renders(): void
    {
        $dto = new DailyEventReportDTO(
            eventId: 1,
            eventName: 'Super Match',
            registrationsCount: 15,
            date: CarbonImmutable::parse('2026-10-21'),
            ownerEmail: 'test@example.com',
            ownerName: 'Alice',
        );

        $mailable = new DailyEventReportMail($dto);
        $mailable->assertSeeInHtml('Bonjour Alice');
        $mailable->assertSeeInHtml('Super Match');
        $mailable->assertSeeInHtml('15');
        $mailable->assertHasSubject("Rapport d'activité : Super Match");
    }
}

