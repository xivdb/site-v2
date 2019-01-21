<?php

namespace XIVDB\Apps\Site;

//
// Cookie class
//
class Cookie extends \XIVDB\Apps\AppHandler
{
    private $defaultExpireTime = TIME_1WEEK;

    public function add($index, $value)
    {
        // Surpressing as headers can be set.
        @setcookie($index, $value, time()+$this->defaultExpireTime, '/', COOKIE_DOMAIN);
    }

    public function get($index)
    {
        return isset($_COOKIE[$index]) ? $_COOKIE[$index] : false;
    }

    public function getAll()
    {
        return $_COOKIE;
    }

    public function remove($index)
    {
        @setcookie($index, $value, time()-1000, '/', COOKIE_DOMAIN);
    }
}
