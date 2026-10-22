<?php

namespace App\Console\Commands;

use App\DTOs\Report\DailyEventReportDTO;
use App\Mail\DailyEventReportMail;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendDailyEventReports extends Command
{
    protected $signature = 'events:send-daily-reports';
    protected $description = 'Envoie un rapport quotidien aux owners dont les événements ont reçu plus de 10 inscriptions la veille';

    public function handle(): int
    {
        $timezone = config('app.timezone', 'UTC');
        $yesterday = CarbonImmutable::yesterday($timezone);
        $startOfDay = $yesterday->startOfDay();
        $endOfDay = $yesterday->endOfDay();

        // Récupération des événements avec le compte des inscriptions confirmées sur la période
        $events = Event::with('user')
            ->withCount([
                'registrations as yesterday_registrations_count' => function ($query) use ($startOfDay, $endOfDay) {
                    $query->where('status', 'confirmed')
                        ->whereBetween('registered_at', [$startOfDay, $endOfDay]);
                },
            ])
            ->whereHas('registrations', function ($query) use ($startOfDay, $endOfDay) {
                $query->where('status', 'confirmed')
                    ->whereBetween('registered_at', [$startOfDay, $endOfDay]);
            }, '>=', 11)
            ->get();

        $dateKey = $yesterday->format('Y-m-d');
        $sentCount = 0;

        foreach ($events as $event) {
            if (! $event->user || ! $event->user->email) {
                continue;
            }

            $cacheKey = "daily_report_sent:event:{$event->id}:{$dateKey}";

            // Idempotence : ajout conditionnel dans le cache avec TTL de 7 jours
            $acquired = Cache::add($cacheKey, true, now()->addDays(7));

            if (! $acquired) {
                $this->line("Rapport déjà envoyé pour l'événement #{$event->id} ({$dateKey}).");
                continue;
            }

            $dto = new DailyEventReportDTO(
                eventId: $event->id,
                eventName: $event->title,
                registrationsCount: (int) $event->yesterday_registrations_count,
                date: $yesterday,
                ownerEmail: $event->user->email,
                ownerName: $event->user->name ?? 'Organisateur',
            );

            Mail::to($dto->ownerEmail)->queue(new DailyEventReportMail($dto));
            $sentCount++;
        }

        $this->info("Traitement terminé : {$sentCount} rapport(s) envoyé(s).");

        return self::SUCCESS;
    }
}
