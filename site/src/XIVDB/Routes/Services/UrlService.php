<?php

namespace XIVDB\Routes\Services;

use Symfony\Component\HttpFoundation\Request,
    Silex\Provider\UrlGeneratorServiceProvider;

//
// UrlService
//
trait UrlService
{
    //
    // Register the url service
    //
    protected function registerUrlService()
    {
        $this->SilexApplication->register(new UrlGeneratorServiceProvider());
    }

    //
    // Set the last url, so login redirects back to the page
    //
    public function setLastUrl(Request $request)
    {
        $cookie = $this->getModule('cookie');
        $cookie->add('lasturl', $request->getUri());
    }

    //
    // Get last url
    //
    public function getLastUrl()
    {
        $cookie = $this->getModule('cookie');
        return $cookie->get('lasturl');
    }

    //
    // Get 1.0 url
    //
    public function v1Route($type)
    {
        $arr = [
            'item' => 'item',
            'recipe' => 'recipe',
            'skill' => 'action',
            'dungeon' => 'instance',
            'achievement' => 'achievement',
            'minion' => 'minion',
            'mount' => 'mount',
            'leve' => 'leve',
            'status' => 'status',
            'huntinglog' => 'huntinglog',
            'npc' => 'npc',
            'monster' => 'enemy',
            'fate' => 'fate',
            'quest' => 'quest',
        ];

        return isset($arr[$type]) ? $arr[$type] : $type;
    }
}
