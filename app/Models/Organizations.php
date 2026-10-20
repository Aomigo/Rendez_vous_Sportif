<?php

namespace App\Models;

use Database\Factories\OrganizationsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'slug', 'plan_premium'])]
class Organizations extends Model
{
    /** @use HasFactory<OrganizationsFactory> */
    use HasFactory, Notifiable;
    protected function casts(): array
    {
        return [
            'plan_premium' => 'boolean',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Events::class, 'organization_id', 'id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Users::class, 'organization_user', 'organization_id', 'user_id')
            ->withPivot('is_owner');
    }

    public function hasMember(Users $user): bool
    {
        return $this->members()->whereKey($user->id)->exists();
    }

    public function isOwnedBy(Users $user): bool
    {
        return $this->members()->whereKey($user->id)->wherePivot('is_owner', true)->exists();
    }
}
