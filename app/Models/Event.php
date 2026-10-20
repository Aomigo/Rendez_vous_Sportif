<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'organization_id',
    'sport_id',
    'created_by',
    'title',
    'description',
    'starts_at',
    'duration_minutes',
    'city',
    'venue',
    'capacity',
    'public_token',
    'status',
])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, Notifiable;

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CANCELLED = 'cancelled';

    public const PARTICIPANT_CONFIRMED = 'confirmed';

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'duration_minutes' => 'integer',
            'capacity' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function organizations(): BelongsTo
    {
        return $this->organization();
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function sports(): BelongsTo
    {
        return $this->sport();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(EventParticipants::class, 'event_id', 'id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'eventParticipants', 'event_id', 'user_id')
            ->withPivot('status', 'registered_at');
    }

    public function confirmedParticipantsCount(): int
    {
        return DB::table('eventParticipants')
            ->where('event_id', $this->id)
            ->whereIn('status', [self::PARTICIPANT_CONFIRMED, 'confirmé', 'confirme'])
            ->count();
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}
