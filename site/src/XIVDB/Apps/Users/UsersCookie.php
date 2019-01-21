<?php

namespace XIVDB\Apps\Users;

//
// Handle user cookie stuff
//
Trait UsersCookie
{
	//
	// Set the users session cookie
	//
	public function setCookie($session, $expire = COOKIE_EXPIRE)
    {
        @setcookie(COOKIE_NAME, $session, (time()+$expire), '/', COOKIE_DOMAIN);
    }

	//
	// Set some other cookie for any kind of data
	//
	public function setCookieMisc($key, $data, $expire = COOKIE_EXPIRE)
	{
		@setcookie($key, $data, (time()+$expire), '/', COOKIE_DOMAIN);
	}

    //
    // Get the users cookie
    //
    public function getCookie($key)
    {
        return trim(@$_COOKIE[$key]);
    }

	//
	// Generate a session id
	//
	public function generateSession()
	{
		$ids = [];
		for($i = 0; $i < 4; $i++) {
			$ids[] = $this->getModule('uuid4');
		}

		return substr(implode('-', $ids), 0, SESSION_LENGTH);
	}
}
