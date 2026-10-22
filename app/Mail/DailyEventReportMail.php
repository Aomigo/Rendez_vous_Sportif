<?php

namespace App\Mail;

use App\DTOs\Report\DailyEventReportDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyEventReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DailyEventReportDTO $report
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Rapport d'activité : {$this->report->eventName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.events.daily-report',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

