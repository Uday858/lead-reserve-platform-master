<?php

namespace App\Providers\Factories;

/**
 * Class UserAgentFactory
 * @package App\Providers\Factories
 */
class UserAgentFactory
{
    /**
     * List of valid user agents.
     */
    const USER_AGENT_STRINGS = [
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36",
        "Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25",
        "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36",
        "Mozilla/5.0 (Linux; U; Android 2.3.3; zh-tw; HTC Pyramid Build/GRI40) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
    ];

    /**
     * Return a random user-agent string.
     *
     * @return mixed
     */
    public static function retrieveRandomAgentString()
    {
        return self::USER_AGENT_STRINGS[rand(0, count(self::USER_AGENT_STRINGS) - 1)];
    }

}