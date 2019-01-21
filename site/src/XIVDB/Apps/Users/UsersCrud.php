<?php

namespace XIVDB\Apps\Users;

/**
 * Trait UsersCrud
 * @package XIVDB\Apps\Users
 */
Trait UsersCrud
{
    /**
     * Create a new user
     *
     *
     * 
     *
     * @param $email
     * @param $username
     * @param $password
     * @return bool|string
     */
	public function createUser($email, $username, $password)
	{
        // Sanitize user input
        list($email, $username, $password) = $this->sanitizeUserCrud($email, $username, $password);

		if ($invalidResponse = $this->invalidUserCrudCheck($email, $username)) {
            return $invalidResponse;
        }

		// password hash
		$password = $this->hashPassword($password);

		// random avatar
		$avatar = mt_rand(1,12);
		$avatar = sprintf('https://secure.xivdb.com/img/avatars/avatar_%s.png', $avatar);

		// create user
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->insert(self::TABLE_MEMBERS)
			->schema(['email', 'username', 'password', 'avatar'])
			->values([$email, $username, $password, $avatar])
			->bind('email', $email)
			->bind('username', $username)
			->bind('password', $password)
			->bind('avatar', $avatar);

		$dbs->execute();
		return 'ok';
	}

    /**
     * @param $email
     * @param $username
     * @param $password
     * @return array
     */
	public function sanitizeUserCrud($email = null, $username = null, $password = null)
    {
        $sanitize = $this->getModule('sanitize');

        if ($email) {
            $email = $sanitize->set($email)->validate('email')->strip()->sanitize()->length(3, 128)->substring(128)->lowercase()->get();
        }

        if ($username) {
            $username = $sanitize->set($username)->validate('text')->strip()->sanitize()->alphanum()->length(3,64)->substring(64)->lowercase()->get();
        }

        if ($password) {
            $password = $sanitize->set($password)->validate('text')->length(3,128)->substring(128)->get();
        }

        return [$email, $username, $password];
    }

    /**
     * @param $email
     * @param $username
     * @param $password
     * @return bool
     */
	public function invalidUserCrudCheck($email, $username)
    {
        $languages = $this->getModule('language');

        // Sanitize user input
        list($email, $username) = $this->sanitizeUserCrud($email, $username);

        // if no email
        if (!$email) {
            return $languages->custom(996);
        }

        // if no username
        if (!$username) {
            return $languages->custom(997);
        }

        return false;
    }

    /**
     * Check if a username exists, will return false if it's the current user
     *
     * @param $userId
     * @param $username
     * @return bool
     */
    public function checkUsernameExists($userId, $username)
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE_MEMBERS)
            ->where(['username = :username'])
            ->bind('username', $username)
            ->limit(0, 1);

        $user = $dbs->get()->one();

        if (!$user) {
            return false;
        }

        // if this user is the owner, return false (can change their own in theory)
        if ($userId !== null && $user['id'] == $userId) {
            return false;
        }

        return true;
    }

    /**
     * Check if an email exists, will return false if it's the current user
     * @param $userId
     * @param $email
     * @return bool
     */
    public function checkEmailExists($userId, $email)
    {
        $email = trim($email);

        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE_MEMBERS)
            ->where(['email = :email'])
            ->bind('email', $email)
            ->limit(0, 1);

        $user = $dbs->get()->one();

        if (!$user) {
            return false;
        }

        // if this user is the owner, return false (can change their own in theory)
        if ($userId !== null && $user['id'] == $userId) {
            return false;
        }

        return true;
    }

    /**
     * Update a users password
     *
     * @param User $user
     * @param $password
     * @return string
     */
    public function updateUserPassword($user, $password)
    {
        $languages = $this->getModule('language');

        if (empty($password) || empty($user)) {
            return '[1] '. $languages->custom(998);
        }

        // Sanitize user input
        list($a, $b, $password) = $this->sanitizeUserCrud(null, null, $password);

        // if invalid password
        if (!$password) {
            return '[2] '. $languages->custom(998);
        }

        // hash password
        $password = $this->hashPassword($password);

        // set
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->update(self::TABLE_MEMBERS)
            ->set([ 'password' => ':password', 'uniq' => 'NULL' ])
            ->bind('password', $password)
            ->where(['id = :id'])
            ->bind('id', is_int($user) ? $user : $user->id);

        $dbs->execute();
        return $languages->custom(1004);
    }

    /**
     * Hash a users password
     *
     * @param $password
     * @return bool|string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }

    /**
     * Add an entry to a users history
     *
     * @param $user
     * @param $content
     * @return mixed
     */
	public function addToHistory($user, $content)
	{
		$dbs = $this->getModule('database');

		// create history event
		$history = [
			microtime(true) => [
				'id' => $content['id'],
				'name' => $content['name'],
				'icon' => $content['icon'],
				'url' => $content['url'],
				'type' => $content['url_type'],
			]
		];

		// if no previous history, create new
		if (!$user->history) {
			$history = json_encode($history);
			$dbs->QueryBuilder
				->update(self::TABLE_MEMBERS)
				->set([ 'history' => ':history' ])
				->bind('history', $history)
				->where('id = :id')
				->bind('id', $user->id);

			return $dbs->execute();
		}

		// get existing history
		$existing = $user->history;

		// ensure new doesn't exist, otherwise remove it
		foreach($existing as $i => $data) {
			if ($data['url'] == $content['url']) {
				unset($existing[$i]);
				break;
			}
		}

		// add to history
		$history = $existing + $history;
		krsort($history);
		array_splice($history, MAX_HISTORY_ITEMS);

		// update
		$history = json_encode($history);
		$dbs->QueryBuilder
			->update(self::TABLE_MEMBERS)
			->set([ 'history' => ':history' ])
			->bind('history', $history)
			->where('id = :id')
			->bind('id', $user->id);

		return $dbs->execute();
	}

    /**
     * Try get a user via a unique token
     *
     * @param $token
     * @return mixed
     */
	public function getUserViaUnique($token)
	{
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE_MEMBERS)
            ->where(['uniq = :uniq'])
            ->bind('uniq', $token)
            ->limit(0,1);

        return $dbs->get()->one();
	}
}
