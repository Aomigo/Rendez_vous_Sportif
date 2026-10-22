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
    public function index(Request $request): View
    {
        // Fetch your events from the database
        $events = $request->user()->events()
            ->where('created_by', auth()->id())
            ->get();

        // Return the view you created at resources/views/events/index.blade.php
        $joinedEvents = $request->user()->joinedEvents()->orderBy('starts_at')->get();

        return view('events.index', compact('events', 'joinedEvents'));
    }

    public function search(Request $request): View
    {
        $filters = $request->only(['sport', 'city', 'date']);

        $events = Event::query()
            ->with(['organization', 'sport'])
            ->available()
            ->filter($filters)
            ->orderBy('starts_at', 'asc')
            ->paginate(12)
            ->withQueryString();

        // Récupère les sports actifs pour le menu déroulant
        $sports = Sport::where('active', true)->orderBy('name')->get();

        return view('events.search', [
            'events' => $events,
            'sports' => $sports,
            'filters' => $filters,
            'joinedEventIds' => $request->user()?->joinedEvents()->pluck('events.id')->all() ?? [],
            'ownedOrganizationIds' => $request->user()?->organizations()->wherePivot('is_owner', true)->pluck('organizations.id')->all() ?? [],
        ]);
    }

    public function details(Request $request, $token): View
    {
        $event = Event::where('public_token', $token)->firstOrFail();
        $user = $request->user();
        $canManage = Gate::allows('manage', $event);

        return view('events.infos', [
            'event' => $event,
            'isRegistered' => $user !== null && $event->participants()->whereKey($user->id)->exists(),
            'canManage' => $canManage,
            'confirmedParticipants' => $canManage
                ? $event->confirmedParticipants()->orderByPivot('registered_at')->get()
                : collect(),
        ]);
    }

    public function join(Request $request, $token)
    {
        $event = Event::where('public_token', $token)->firstOrFail();

        $event->participants()->attach(auth()->id(), [
            'registered_at' => now(),
        ]);

        return back()->with('status', 'Vous êtes bien inscrit à l\'événement !');
    }

    public function leave($token)
    {
        $event = Event::where('public_token', $token)->firstOrFail();
        $event->participants()->detach(auth()->id());

        return back()->with('status', 'Vous avez quitté l\'événement.');
    }

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

        return redirect()->route('events.details', $event->public_token)->with('status', 'Événement mis à jour.');
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
