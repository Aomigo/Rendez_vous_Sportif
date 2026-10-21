<?php

namespace App\Http\Requests\Event;

use App\DTOs\Event\EventDTO;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', [Event::class, $this->organization()]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_id' => ['sometimes', 'nullable', 'integer', 'exists:organizations,id'],
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'starts_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'between:30,240'],
            'city' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'between:2,100'],
        ];
    }

    public function organization(): ?Organization
    {
        if ($this->filled('organization_id')) {
            return Organization::find($this->input('organization_id'));
        }

        return $this->user()?->currentOrganization();
    }

    public function toDTO(): EventDTO
    {
        return EventDTO::fromArray($this->validated());
    }
}
