<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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

    public const string STATUS_PUBLISHED = 'published';

    public const string STATUS_CANCELLED = 'cancelled';

    public const string PARTICIPANT_CONFIRMED = 'confirmed';

    public const  string STATUS_CLOSED = 'closed';

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

    // Accesseur pour obtenir directement $event->remaining_seats dans Blade
    public function getRemainingSeatsAttribute(): int
    {
        $count = $this->confirmed_participants_count ?? $this->confirmedParticipantsCount();

        return max(0, $this->capacity - $count);
    }

    // Scope critères d'éligibilité (sans HAVING pour éviter l'erreur MySQL 1463 lors de la pagination)
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('starts_at', '>', now())
            ->withCount([
                'participants as confirmed_participants_count' => function ($q) {
                    $q->whereIn('eventParticipants.status', [
                        self::PARTICIPANT_CONFIRMED,
                        'confirmé',
                        'confirme',
                    ]);
                },
            ])
            ->whereRaw('capacity > (
                select count(*) from `eventParticipants`
                where `eventParticipants`.`event_id` = `events`.`id`
                and `eventParticipants`.`status` in (?, ?, ?)
            )', [self::PARTICIPANT_CONFIRMED, 'confirmé', 'confirme']);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['sport'] ?? null, function (Builder $q, $sport) {
                if (is_numeric($sport)) {
                    $q->where('sport_id', $sport);
                } else {
                    $q->whereHas('sport', fn ($sq) => $sq->where('name', $sport));
                }
            })
            ->when($filters['city'] ?? null, function (Builder $q, $city) {
                $q->where('city', 'like', "%{$city}%");
            })
            ->when($filters['date'] ?? null, function (Builder $q, $date) {
                $q->whereDate('starts_at', $date);
            });
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }
}
