<?php


namespace DassumanLaravelTickets\Traits;


use Illuminate\Database\Eloquent\Relations\HasMany;
use DassumanLaravelTickets\Models\Ticket;

/**
 * Trait HasTickets
 *
 * Extends the user model with functions
 *
 * @package DassumanLaravelTickets\Traits
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
