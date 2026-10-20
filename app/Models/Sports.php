<?php

namespace App\Models;

use Database\Factories\SportsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name'])]
class Sports extends Model
{
    /** @use HasFactory<SportsFactory> */
    use HasFactory, Notifiable;
    public function events(): HasMany
    {
        return $this->hasMany(Events::class, 'sport_id', 'id');
    }
}
