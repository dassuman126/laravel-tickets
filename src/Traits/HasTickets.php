<?php


namespace Dassuman\LaravelTickets\Traits;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Dassuman\LaravelTickets\Models\Ticket;

/**
 * Trait HasTickets
 *
 * Extends the user model with functions
 *
 * @package Dassuman\LaravelTickets\Traits
 */
trait HasTickets
{

    /**
     * Gives every ticket that belongs to user
     *
     * @return HasMany
     */
    function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

}
