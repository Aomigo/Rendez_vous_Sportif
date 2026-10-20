<?php

namespace App\Http\Requests\Event;

use App\DTOs\Event\EventDTO;
use App\Models\Event;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Event $event */
        $event = $this->route('event');

        return [
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'starts_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'between:30,240'],
            'city' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'capacity' => [
                'required',
                'integer',
                'between:2,100',
                function (string $attribute, mixed $value, Closure $fail) use ($event) {
                    $confirmedCount = $event->confirmedParticipantsCount();
                    if ((int) $value < $confirmedCount) {
                        $fail("La capacité ne peut pas être inférieure au nombre d'inscriptions confirmées ({$confirmedCount}).");
                    }
                },
            ],
        ];
    }

    public function toDTO(): EventDTO
    {
        return EventDTO::fromArray($this->validated());
    }
}

