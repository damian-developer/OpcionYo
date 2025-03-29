<?php

namespace App\Listeners;

use App\Events\NewAppointment;
use App\Mail\EmailForAnAppointment;
use Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailForAnAppointment
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewAppointment $event): void
    {
        Mail::to($event->appointment->doctor->user->email)->send(new EmailForAnAppointment($event->appointment));
    }
}
