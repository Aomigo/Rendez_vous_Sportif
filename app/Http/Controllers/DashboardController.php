<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $organization = $request->user()->currentOrganization();

        if ($organization === null) {
            abort_unless($request->user()->is_admin, 403);

            return view('dashboard.index', ['organization' => null, 'eventGroups' => []]);
        }

        Gate::authorize('viewDashboard', [Event::class, $organization]);

        $events = $organization->events()
            ->with('sport')
            ->withCount('confirmedParticipants')
            ->orderBy('starts_at')
            ->get();

        return view('dashboard.index', [
            'organization' => $organization,
            'eventGroups' => [
                'À venir' => $events->filter(fn (Event $event) => $event->isPublished() && ! $event->isPast()),
                'Terminés' => $events->filter(fn (Event $event) => $event->isPublished() && $event->isPast())->reverse(),
                'Annulés' => $events->filter(fn (Event $event) => $event->isCancelled()),
            ],
        ]);
    }
}
