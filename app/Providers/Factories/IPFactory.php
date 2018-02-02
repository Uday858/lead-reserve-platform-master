<?php

namespace App\Providers\Factories;

/**
 * Class UserAgentFactory
 * @package App\Providers\Factories
 */
class IPFactory
{
    /**
     * Retrieve random IPString.
     * @return string
     */
    public static function retrieveIPString()
    {
        return rand(23,255) . "." . rand(23,255) . "." . rand(23,255) . "." . rand(23,255);
    }
}