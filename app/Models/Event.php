<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, Notifiable;

    public function users(): HasMany
    {
        return $this->hasMany(EventParticipants::class, 'event_id', 'id');
    }

    public function organizations(): HasOne
    {
        return $this->hasOne(Organization::class);
    }
    public function sports(): HasOne
    {
        return $this->hasOne(Sport::class);
    }
}
