<?php


namespace Dassuman\LaravelTickets\Models;


use Illuminate\Database\Eloquent\Model;
use Dassuman\LaravelTickets\Traits\HasConfigModel;

/**
 * Class TicketCategory
 *
 * Used for declaring a ticket to a specific topic
 *
 * @package Dassuman\LaravelTickets\Models
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
