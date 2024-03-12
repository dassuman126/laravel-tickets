<?php


namespace DassumanLaravelTickets\Observers;


use DassumanLaravelTickets\Models\TicketUpload;

class TicketUploadObserver
{

    public function deleting(TicketUpload $ticketUpload)
    {
        \Storage::disk(config('laravel-tickets.file.driver'))->delete($ticketUpload->path);
    }

}
