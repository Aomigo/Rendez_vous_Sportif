<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'is_admin'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_user', 'user_id', 'organization_id')
            ->withPivot('is_owner');
    }

    /**
     * Organisation choisie en session, ou la première du user si le choix n'est plus valide.
     */
    public function currentOrganization(): ?Organization
    {
        return $this->organizations()->find(session('current_organization_id'))
            ?? $this->organizations()->orderBy('name')->first();
    }

    public function joinedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'eventParticipants', 'user_id', 'event_id')
            ->withPivot('status', 'registered_at');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by', 'id');
    }
}
