<?php


namespace Dassuman\LaravelTickets\Observers;


use Dassuman\LaravelTickets\Models\TicketUpload;

class TicketUploadObserver
{

    public function deleting(TicketUpload $ticketUpload)
    {
        \Storage::disk(config('laravel-tickets.file.driver'))->delete($ticketUpload->path);
    }

}
