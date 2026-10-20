<?php

namespace App\Models;

use Database\Factories\organizationsFactory;
use Database\Factories\usersFactory;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'slug', 'plan_premium'])]
class Organizations extends Model
{
    /** @use HasFactory<organizationsFactory> */
    use HasFactory, Notifiable;
    protected function casts(): array
    {
        return [
            'plan_premium' => 'boolean',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organization_id', 'id');
    }
}
