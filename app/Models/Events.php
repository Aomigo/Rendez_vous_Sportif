<?php

namespace App\Models;

use Database\Factories\eventsFactory;
use Database\Factories\usersFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Events extends Model
{
    /** @use HasFactory<eventsFactory> */
    use HasFactory, Notifiable;

    public function users(): HasMany
    {
        return $this->hasMany(EventParticipants::class, 'event_id', 'id');
    }

    public function organizations(): HasOne
    {
        return $this->hasOne(Organizations::class);
    }
    public function sports(): HasOne
    {
        return $this->hasOne(Sports::class);
    }
}
