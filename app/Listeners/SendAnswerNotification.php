<?php

namespace App\Listeners;

use App\Events\AnswerCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAnswerNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AnswerCreated  $event
     * @return void
     */
    public function handle(AnswerCreated $event)
    {
        Log::debug($event->answer);
    }
}
