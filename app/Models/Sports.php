<?php

namespace App\Models;

use Database\Factories\sportsFactory;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name'])]
class Sports extends Model
{
    /** @use HasFactory<sportsFactory> */
    use HasFactory, Notifiable;
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'sports_id', 'id');
    }
}
