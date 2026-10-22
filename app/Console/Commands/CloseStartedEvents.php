<?php

namespace App\Console\Commands;

use App\Actions\Event\EventClosed;
use App\Models\Event;
use Illuminate\Console\Command;

class CloseStartedEvents extends Command
{
    protected $signature = 'events:close-started';

    protected $description = 'Close events that have started and are not yet closed.';

    public function handle(): void
    {
$events = Event::where('starts_at', '<=', now())
    ->where('status', '!=', Event::STATUS_CLOSED)
    ->get();

        foreach ($events as $event) {
            $event->update([
                'status' => Event::STATUS_CLOSED,
            ]);

            EventClosed::dispatch($event);
        }

        $this->info("{$events->count()} event(s) closed.");
    }
}
