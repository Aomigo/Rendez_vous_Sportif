<?php

namespace App\DTOs\Report;

use Carbon\CarbonImmutable;

readonly class DailyEventReportDTO
{
    public function __construct(
        public int $eventId,
        public string $eventName,
        public int $registrationsCount,
        public CarbonImmutable $date,
        public string $ownerEmail,
        public string $ownerName,
    ) {}
}

