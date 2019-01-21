<?php

namespace XIVDB\Apps\Users;

/**
 * Class Users
 * @package XIVDB\Apps\Users
 */
class Users extends \XIVDB\Apps\AppHandler
{
	use UsersCookie;
	use UsersCrud;
	use UsersEmails;
	use UsersAuthorize;
	use UsersCharacters;

	const TABLE_MEMBERS = 'members';
	const TABLE_SESSIONS = 'members_sessions';
	const TABLE_CHARACTERS = 'members_characters';

	// All fields related to users and characters
	protected $fieldsMember = [
		'id', 'email', 'username', 'password', 'lastonline', 'added',
		'avatar', 'lodestone_id', 'character_name', 'character_server',
		'character_avatar', 'banned', 'suspended', 'star', 'approved',
		'moderator', 'admin', 'active', 'data',
		'uniq', 'history', 'preferences',
	];
	protected $fieldsCharacter = [
		'member', 'lodestone_id', 'character_name', 'character_server',
		'character_portrait', 'character_avatar', 'character_is_main',
	];

    /**
     * Get a user
     * @param null $id
     * @param bool $simple
     * @return bool|User
     */
	public function get($id = null, $simple = false)
	{
		// if user is online or not
		$isOnline = false;
		$dbs = $this->getModule('database');

		// if no id, try session
		if (!$id && $cookie = $this->getCookie(COOKIE_NAME)) {
			$isOnline = true;
			$dbs->QueryBuilder
				->select('*', false)
				->from(self::TABLE_MEMBERS)
				->join([ self::TABLE_MEMBERS => 'id' ], [ self::TABLE_SESSIONS => 'member' ])
				->where(sprintf('%s.session = :cookie', self::TABLE_SESSIONS))
				->bind('cookie', $cookie)
				->limit(0, 1);
		}
		// else if ID is passed
		elseif ($id) {
			$isOnline = true;
			$dbs->QueryBuilder
				->select('*', false)
				->from(self::TABLE_MEMBERS)
				->where('id = :id')->bind(':id', $id)
				->limit(0, 1);
		}

		// if user data
		if ($isOnline && $userData = $dbs->get()->one()) {
			$user = new User($userData, $simple);

			// get additional stuff
			if (!$simple) {
				$user->getCharacters();
				$user->getShoppingCarts();
				$user->getGearsets();
			}

			return $user;
		}

		return false;
	}

    /**
     * Get user by username
     *
     * @param $username
     * @return bool|User
     */
	public function getByUsername($username)
	{
		$dbs = $this->getModule('database');

		$dbs->QueryBuilder
			->select('*', false)
			->from(self::TABLE_MEMBERS)
			->where('username = :un')
			->bind('un', $username)
			->limit(0, 1);

		if ($userData = $dbs->get()->one()) {
			$user = new User($userData, true);
			return $user;
		}

		return false;
	}

    /**
     * Save a user
     *
     * @return $this
     */
	public function save()
    {
        // don't save anything without a set id
        if (!$this->id) {
            die;
        }

        // Data set manually, some day will be automatic...
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->update(self::TABLE_MEMBERS)
            ->set([
                'uniq' => ':uniq',
                'email' => ':email',
                'username' => ':username',
                'lastonline' => ':lastonline',
                'added' => ':added',
                'avatar' => ':avatar',
                'lodestone_id' => ':lodestone_id',
                'character_name' => ':character_name',
                'character_server' => ':character_server',
                'character_avatar' => ':character_avatar',
                'banned' => ':banned',
                'suspended' => ':suspended',
                'star' => ':star',
                'moderator' => ':moderator',
                'admin' => ':admin',
                'active' => ':active',
                'data' => ':data',
                'history' => ':history'
            ])
            ->bind('uniq', $this->uniq)
            ->bind('email', $this->email)
            ->bind('username', $this->username)
            ->bind('lastonline', $this->lastonline)
            ->bind('added', $this->added)
            ->bind('avatar', $this->avatar)
            ->bind('lodestone_id', $this->lodestone_id)
            ->bind('character_name', $this->character_name)
            ->bind('character_server', $this->character_server)
            ->bind('character_server', $this->character_server)
            ->bind('character_avatar', $this->character_avatar)
            ->bind('banned', $this->banned)
            ->bind('suspended', $this->suspended)
            ->bind('star', $this->star)
            ->bind('moderator', $this->moderator)
            ->bind('admin', $this->admin)
            ->bind('active', $this->active)
            ->bind('data', json_encode($this->data))
            ->bind('history', json_encode($this->history))
            ->where(['id = :id'])
            ->bind('id', $this->id);

        $dbs->execute();
        return $this;
    }

    /**
     * Get characters for a user
     *
     * @param null $id
     * @return array|Users
     */
	public function getCharacters($id = null)
	{
		$dbs = $this->getModule('database');

		// if id passed, use that, otherwise try local id.
		$isLocal = $id ? false : true;
		$id = $id ? $id : $this->id;

		// get characters
		$dbs->QueryBuilder
			->select('*', false)
			->from(self::TABLE_CHARACTERS)
			->where('member = :id')->bind(':id', $id)
			->order('lodestone_id', 'asc');

		// get a list of characters as UserCharacter classes
		$list = [];
		if ($characterData = $dbs->get()->all()) {
			foreach($characterData as $character) {
				$list[$character['lodestone_id']] = new UserCharacter($character);
			}
		}

		// if called locally, set array
		if ($isLocal) {
			$this->characters = $list;
		}

		// return either this or the list of characters
		// depending on if this was called locally.
		return $isLocal ? $this : $list;
	}

    /**
     * Get comments for a user
     *
     * @param null $id
     * @return Users
     */
	public function getComments($id = null)
	{
		// if id passed, use that, otherwise try local id.
		$isLocal = $id ? false : true;
		$id = $id ? $id : $this->id;

		$comments = $this->getModule('comments');
		$comments = $comments->getViaUserId($id);

		// if the user has comments, get the content for those comments.
		if ($comments)
		{
			$content = $this->getModule('content');

			foreach($comments as $i => $cmt) {
				$comments[$i]['content'] = $content->setCid($cmt['content'])->getContentToId($cmt['content'], $cmt['uniq']);
			}
		}

		// if called locally, set array
		if ($isLocal) {
			$this->comments = $comments;
		}

		// return either this or the list of characters
		// depending on if this was called locally.
		return $isLocal ? $this : $comments;
	}

    /**
     * Get screenshots for a user
     *
     * @param null $id
     * @return Users
     */
	public function getScreenshots($id = null)
	{
		// if id passed, use that, otherwise try local id.
		$isLocal = $id ? false : true;
		$id = $id ? $id : $this->id;

		$screenshots = $this->getModule('screenshots');
		$screenshots = $screenshots->getViaUserId($id);

		// if the user has screenshots, get the content for those screenshots.
		if ($screenshots)
		{
			$content = $this->getModule('content');

			foreach($screenshots as $i => $cmt) {
				$screenshots[$i]['content'] = $content->setCid($cmt['content'])->getContentToId($cmt['content'], $cmt['uniq']);
			}
		}

		// if called locally, set array
		if ($isLocal) {
			$this->screenshots = $screenshots;
		}

		// return either this or the list of characters
		// depending on if this was called locally.
		return $isLocal ? $this : $screenshots;
	}

    /**
     * Get shopping cart for a user
     *
     * @param null $id
     * @return Users
     */
	public function getShoppingCarts($id = null)
	{
		$dbs = $this->getModule('database');

		// if id passed, use that, otherwise try local id.
		$isLocal = $id ? false : true;
		$id = $id ? $id : $this->id;

		// get all saved carts
		$dbs->QueryBuilder
			->reset()
			->select('*', false)
			->from('content_shopping_cart')
			->where('user = :id')->bind(':id', $id)
			->order('updated', 'desc');

		$list = $dbs->get()->all();

		// if called locally, set array
		if ($isLocal) {
			$this->shoppingCarts = $list;
		}

		// return either this or the list of characters
		// depending on if this was called locally.
		return $isLocal ? $this : $list;
	}

    /**
     * Get custom gearsets for a user
     *
     * @param null $id
     * @return Users
     */
	public function getGearsets($id = null)
	{
		$dbs = $this->getModule('database');

		// if id passed, use that, otherwise try local id.
		$isLocal = $id ? false : true;
		$id = $id ? $id : $this->id;

		// get all saved carts
		$dbs->QueryBuilder
			->reset()
			->select('*', false)
			->from('content_gearsets')
			->where('user = :id')->bind(':id', $id)
			->order('updated', 'desc');

		$list = $dbs->get()->all();

		// if called locally, set array
		if ($isLocal) {
			$this->gearsets = $list;
		}

		// return either this or the list of characters
		// depending on if this was called locally.
		return $isLocal ? $this : $list;
	}
}
