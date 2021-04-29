<?php

namespace App\Listeners;

use App\Events\AnswerCreated;
use App\Traits\SendsNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAnswerNotification
{
    use SendsNotifications;
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
        $this->sendNotificationToUser(
            $event->answer->question->author,
            [
                "title" => "Nová odpoveď",
                "body" => "Používateľ " . $event->answer->author->name . " pridal odpoveď na vašu otázku",
                "answer_id" => $event->answer->id,
                "question_id" =>  $event->answer->question->id
            ]
        );
    }
}
