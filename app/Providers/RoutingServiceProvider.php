<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class RoutingServiceProvider
{
    /**
     * @param $url
     * @return string
     */
    public function isActiveUrl($url)
    {
        return (Request::url() === $url) ? 'active' : '';
    }
}
