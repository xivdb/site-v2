<?php

namespace XIVDB\Apps\Users;

/**
 * Class User
 * @package XIVDB\Apps\Users
 */
Class User extends Users
{
	public $id;
	public $uniq;
    public $email;
    public $username;
    public $password;
    public $lastonline;
    public $added;
    public $avatar;

    public $lodestone_id;
    public $character_name;
    public $character_server;
    public $character_avatar;

    public $banned = false;
    public $suspended = false;
    public $star = false;
    public $moderator = false;
    public $admin = false;
    public $active = false;

    public $data = [];
    public $history = [];

	// an array of characters attached to this account
	public $characters = [];

	// an array of shopping carts saved on this account
	public $shoppingCarts = [];

	// an array of gearsets saved on this account
	public $gearsets = [];

	// an array of community stuff
	public $comments = [];
	public $screenshots = [];

	// custom stuff not in database
	public $characterCode;
	public $url;
    public $character_url;

    /**
     * User constructor.
     * @param $data
     * @param bool $simple
     */
	function __construct($data, $simple = false)
	{
		foreach($data as $key => $value) {
			$this->$key = $value;
		}

		// don't need.
		unset($this->password);

		// this will suffice, it doesn't need
		// to be fully unique, mostly just identifiable.
		$this->characterCode = substr(sha1($this->email), 0, 10);

		// set url to the users profile
		$this->url = $this->url('profile', $this->username);
        $end = sprintf('%s/%s', $this->character_name, $this->character_server);
        $this->character_url = $this->url('character', $this->lodestone_id, $end);

		// Json decode some data
        $this->data = $simple ? [] : json_decode($this->data, true);
        $this->history = $simple ? [] : json_decode($this->history, true);
	}

    /**
     * @return bool
     */
	public function isAdmin()
	{
		return $this->admin;
	}

    /**
     * @return bool
     */
	public function isModerator()
	{
		return ($this->moderator || $this->admin);
	}

    /**
     * @return bool
     */
	public function hasMainCharacter()
	{
		return $this->lodestone_id ? true : false;
	}

    /**
     * @return array
     */
	public function toArray()
	{
		return [
			'id' => $this->id,
		    'email' => $this->email,
		    'username' => $this->username,
		    'lastonline' => $this->lastonline,
		    'added' => $this->added,
		    'avatar' => $this->avatar,
		    'banned' => $this->banned,
		    'suspended' => $this->suspended,
		    'star' => $this->star,
		    'approved' => $this->approved,
		    'moderator' => $this->moderator,
		    'admin' => $this->admin,
		    'active' => $this->active,
		    'data' => $this->data ? json_decode($this->data, true) : [],
		    'history' => $this->history ? json_decode($this->history, true) : [],
			'url' => $this->url,
		];
	}

    /**
     * Set unique instance
     * @return $this
     */
    public function setUnique()
    {
        $this->uniq = $this->generateSession();
        $this->save();
        return $this;
    }

    public function setLastOnline()
    {
        $this->lastonline = date(DATE_MYSQL);
        $this->save();
        return $this;
    }

    /**
     * Ban a user
     * @return $this
     */
    public function ban()
    {
        $this->banned = 1;
        $this->save();
        return $this;
    }

    /**
     * Suspend a user
     * @return $this
     */
    public function suspend()
    {
        $this->suspended = 1;
        $this->save();
        return $this;
    }

    /**
     * Star a user
     * @return $this
     */
    public function star()
    {
        $this->star = 1;
        $this->save();
        return $this;
    }

    /**
     * Make a user an moderator
     * @return $this
     */
    public function makeModerator()
    {
        $this->moderator = 1;
        $this->save();
        return $this;
    }

    /**
     * Make a user an admin
     * @return $this
     */
    public function makeAdmin()
    {
        $this->admin = 1;
        $this->save();
        return $this;
    }
}
