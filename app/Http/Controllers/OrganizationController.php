<?php

namespace App\Http\Controllers;

use App\Actions\Organization\AddOrganizationMemberAction;
use App\Actions\Organization\DeleteOrganizationAction;
use App\Actions\Organization\RemoveOrganizationMemberAction;
use App\Actions\Organization\StoreOrganizationAction;
use App\Actions\Organization\UpdateOrganizationAction;
use App\DTOs\Organization\OrganizationDTO;
use App\DTOs\Organization\OrganizationMemberDTO;
use App\Http\Requests\Organization\AddOrganizationMemberRequest;
use App\Http\Requests\Organization\DeleteOrganization;
use App\Http\Requests\Organization\RemoveOrganizationMemberRequest;
use App\Http\Requests\Organization\StoreOrganization;
use App\Http\Requests\Organization\UpdateOrganization;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $organizations = $request->user()->organizations()
            ->withCount('members')
            ->orderBy('name')
            ->get()
            ->map(fn (Organization $organization) => OrganizationDTO::fromModel($organization, $organization->pivot->is_owner));

        return view('organizations.index', [
            'organizations' => $organizations,
            'currentOrganizationId' => $request->user()->currentOrganization()?->id,
        ]);
    }

    public function create(): View
    {
        return view('organizations.create');
    }

    public function store(StoreOrganization $request, StoreOrganizationAction $action): RedirectResponse
    {
        $organization = $action->handle($request->user(), $request->validated('name'));

        $request->session()->put('current_organization_id', $organization->id);

        return redirect()->route('organizations.index')->with('status', 'Organisation créée.');
    }

    public function edit(Request $request, Organization $organization): View
    {
        Gate::authorize('update', $organization);

        return view('organizations.edit', [
            'organization' => OrganizationDTO::fromModel($organization->loadCount('members'), true),
            'members' => $organization->members()->orderBy('name')->get()->map(OrganizationMemberDTO::fromModel(...)),
        ]);
    }

    public function update(UpdateOrganization $request, Organization $organization, UpdateOrganizationAction $action): RedirectResponse
    {
        $action->handle($organization, $request->validated('name'));

        return redirect()->route('organizations.edit', $organization)->with('status', 'Organisation mise à jour.');
    }

    public function destroy(DeleteOrganization $request, Organization $organization, DeleteOrganizationAction $action): RedirectResponse
    {
        $action->handle($organization);

        return redirect()->route('organizations.index')->with('status', 'Organisation supprimée.');
    }

    public function switch(Request $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('view', $organization);

        $request->session()->put('current_organization_id', $organization->id);

        return back()->with('status', 'Organisation active : '.$organization->name);
    }

    public function addMember(AddOrganizationMemberRequest $request, Organization $organization, AddOrganizationMemberAction $action): RedirectResponse
    {
        $action->handle($organization, $request->validated('email'));

        return redirect()->route('organizations.edit', $organization)->with('status', 'Membre ajouté.');
    }

    public function removeMember(RemoveOrganizationMemberRequest $request, Organization $organization, User $user, RemoveOrganizationMemberAction $action): RedirectResponse
    {
        $action->handle($organization, $user);

        return redirect()->route('organizations.edit', $organization)->with('status', 'Membre retiré.');
    }
}
