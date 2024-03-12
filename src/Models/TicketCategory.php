<?php


namespace DassumanLaravelTickets\Models;


use Illuminate\Database\Eloquent\Model;
use DassumanLaravelTickets\Traits\HasConfigModel;

/**
 * Class TicketCategory
 *
 * Used for declaring a ticket to a specific topic
 *
 * @package DassumanLaravelTickets\Models
 */
class TicketCategory extends Model
{

    use HasConfigModel;

    protected $fillable = [
        'translation'
    ];

    public function getTable()
    {
        return config('laravel-tickets.database.ticket-categories-table');
    }
}
