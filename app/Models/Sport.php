<?php

namespace App\Models;

use Database\Factories\SportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'slug', 'active'])]
class Sport extends Model
{
    /** @use HasFactory<SportFactory> */
    use HasFactory, Notifiable;
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'sport_id', 'id');
    }
}
