<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UsersFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'is_admin'])]
class Users extends Authenticatable
{
    /** @use HasFactory<UsersFactory> */
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
        return $this->belongsToMany(Organizations::class, 'organization_user', 'user_id', 'organization_id')
            ->withPivot('is_owner');
    }

    /**
     * Organisation choisie en session, ou la première du user si le choix n'est plus valide.
     */
    public function currentOrganization(): ?Organizations
    {
        return $this->organizations()->find(session('current_organization_id'))
            ?? $this->organizations()->orderBy('name')->first();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Events::class, 'created_by', 'id');
    }

}
