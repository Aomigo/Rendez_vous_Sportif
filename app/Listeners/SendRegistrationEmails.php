<?php

namespace App\Listeners;

use App\Events\ParticipantRegistered;
use App\Mail\OwnerRegistrationNoticeMail;
use App\Mail\ParticipantRegistrationMail;
use Illuminate\Support\Facades\Mail;

class SendRegistrationEmails
{
    public function handle(ParticipantRegistered $registered): void
    {
        Mail::to($registered->participant)->queue(new ParticipantRegistrationMail($registered->event, $registered->participant));

        $owners = $registered->event->organization->members()
            ->wherePivot('is_owner', true)
            ->whereKeyNot($registered->participant->id)
            ->get();

        foreach ($owners as $owner) {
            Mail::to($owner)->queue(new OwnerRegistrationNoticeMail($registered->event, $registered->participant));
        }
    }
}
