<?php


namespace Dassuman\LaravelTickets\Traits;

/**
 * Trait HasConfigModel
 *
 * Is used internal for using configuration elements
 *
 * @package Dassuman\LaravelTickets\Traits
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
