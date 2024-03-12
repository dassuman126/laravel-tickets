<?php


namespace Dassuman\LaravelTickets\Observers;


use Dassuman\LaravelTickets\Events\TicketMessageEvent;
use Dassuman\LaravelTickets\Models\TicketActivity;
use Dassuman\LaravelTickets\Models\TicketMessage;
use Dassuman\LaravelTickets\Models\TicketUpload;
use Storage;

class TicketMessageObserver
{

    public function created(TicketMessage $ticketMessage)
    {
        $ticketActivity = new TicketActivity([ 'type' => 'ANSWER' ]);
        $ticketActivity->ticket()->associate($ticketMessage->ticket()->first());
        $ticketActivity->targetable()->associate($ticketMessage);
        $ticketActivity->save();

        $ticket = $ticketMessage->ticket()->first();

        if ($ticketMessage->user_id != $ticket->user_id) {
            $ticket->update([ 'state' => 'ANSWERED' ]);
        }

        event(new TicketMessageEvent($ticket, $ticketMessage));
    }

    public function deleting(TicketMessage $ticketMessage)
    {
        $ticketMessage->uploads()->get()->each(fn(TicketUpload $ticketUpload) => $ticketUpload->delete());
        Storage::disk(config('laravel-tickets.file.driver'))
            ->deleteDirectory(config('laravel-tickets.file.path') . $ticketMessage->id);
    }

}
