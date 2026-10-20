<?php

namespace App\Http\Controllers;

use App\Actions\Event\CancelEventAction;
use App\Actions\Event\StoreEventAction;
use App\Actions\Event\UpdateEventAction;
use App\DTOs\Event\EventDTO;
use App\Http\Requests\Event\CancelEventRequest;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventController extends Controller
{
    public function create(Request $request): View
    {
        $organization = $request->user()->currentOrganization();

        Gate::authorize('create', [Event::class, $organization]);

        return view('events.create', [
            'organization' => $organization,
            'sports' => Sport::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEventRequest $request, StoreEventAction $action): RedirectResponse
    {
        $event = $action->handle(
            $request->user(),
            $request->organization(),
            $request->toDTO(),
        );

        return redirect()->route('events.edit', $event)->with('status', 'Événement publié.');
    }

    public function edit(Request $request, Event $event): View
    {
        Gate::authorize('update', $event);

        return view('events.edit', [
            'event' => EventDTO::fromModel($event),
            'sports' => Sport::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event, UpdateEventAction $action): RedirectResponse
    {
        $action->handle($event, $request->toDTO());

        return redirect()->route('events.edit', $event)->with('status', 'Événement mis à jour.');
    }

    public function cancel(CancelEventRequest $request, Event $event, CancelEventAction $action): RedirectResponse
    {
        $action->handle($event);

        return redirect()->route('events.edit', $event)->with('status', 'Événement annulé.');
    }

    public function destroy(CancelEventRequest $request, Event $event, CancelEventAction $action): RedirectResponse
    {
        return $this->cancel($request, $event, $action);
    }
}

