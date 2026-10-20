<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['event_id', 'user_id', 'status', 'registered_at'])]
class EventParticipants extends Model
{
    protected $table = 'eventParticipants';

    public $timestamps = false;
}
