<?php


namespace Dassuman\LaravelTickets\Helper;

/**
 * Class ReferenceHelper
 *
 * Provides helper functions for the ticket reference
 *
 * @package Dassuman\LaravelTickets\Helper
 */
class ReferenceHelper
{

    public static function modelToReference($model) : string
    {
        $type = get_class($model);
        return "$type,$model->id";
    }


}
