<?php


namespace DassumanLaravelTickets\Traits;

/**
 * Trait HasConfigModel
 *
 * Is used internal for using configuration elements
 *
 * @package DassumanLaravelTickets\Traits
 */
trait HasConfigModel
{

    public function getKeyType()
    {
        return 'string';
    }

    public function isIncrementing()
    {
        return config('laravel-tickets.model.incrementing');
    }
}
