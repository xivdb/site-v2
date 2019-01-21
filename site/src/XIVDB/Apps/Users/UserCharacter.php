<?php

namespace XIVDB\Apps\Users;

//
// A user character class
//
class UserCharacter extends Users
{
	public $member;
	public $lodestone_id;
    public $character_name;
    public $character_server;
    public $character_avatar;
    public $character_portrait;
    public $character_is_main;
	public $url;

	function __construct($data)
	{
		foreach($data as $key => $value) {
			$this->$key = $value;
		}

		$end = sprintf('%s/%s', $this->character_name, $this->character_server);
		$this->url = $this->url('character', $this->lodestone_id, $end);
	}
}
