<?php


namespace Dassuman\LaravelTickets\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Dassuman\LaravelTickets\Models\Ticket;
use Dassuman\LaravelTickets\Models\TicketMessage;

/**
 * Class TicketMessageEvent
 *
 * Fired when a ticket gets answered by a user
 *
 * @package Dassuman\LaravelTickets\Events
 */
class TicketMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, TicketMessage $ticketMessage)
    {
        $this->ticket = $ticket;
        $this->message = $ticketMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('ticket-message');
    }
}
