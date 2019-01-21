<?php

namespace XIVDB\Routes\Modules;

//
// Access to user functions
//
trait UserModule
{
    //
    // Add some content to the users history
    //
    public function addToHistory($content)
    {
        // if user is online, add content to history
        if ($this->getUser()) {
            $users = $this->getModule('users');
            $users->addToHistory($this->getUser(), $content);
        }
    }
}
