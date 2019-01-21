<?php

namespace XIVDB\Apps\Users;

//
// User authorize operations
//
Trait UsersAuthorize
{
	//
    // Check a recaptcha request
    //
    public function recaptcha($response)
    {
        // setup post data
        $postdata = http_build_query([
            'secret' => RECAPTCHA_SECRET,
            'response' => $response,
        ]);

        // setup options
        $options = [
            'http' => [
                'method' => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            ],
        ];

        // test
        $context  = stream_context_create($options);
        $result = file_get_contents(RECAPTCHA_URL, false, $context);
        $result = json_decode($result, true);

        // return
        return $result['success'];
    }

    //
    // Authorize a login attempt
    //
    public function authorizeLogin($login, $password)
    {
        $languages = $this->getModule('language');
        $sanitize = $this->getModule('sanitize');

        // sanitize
        $login      = $sanitize->set($login)->validate('text')->strip()->sanitize()->length(3,128)->substring(128)->lowercase()->get();
        $password   = $sanitize->set($password)->validate('text')->length(3,128)->substring(128)->get();

        // if invalid login
        if (!$login) {
            return $languages->custom(1013);
        }

        // if invalid password
		if (!$password) {
			return $languages->custom(998);
		}

        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE_MEMBERS)
            ->where(['username = :p1', 'email = :p2'], 'OR')
            ->bind('p1', $login)
            ->bind('p2', $login)
            ->limit(0,1);

        $account = $dbs->get()->one();
        if (!$account) {
            return $languages->custom(1009);
        }

        // verify password
        if (password_verify($password, $account['password'])) {
            // generate session
            $session = $this->generateSession();
            $this->setCookie($session);

            // insert members session
            $dbs->QueryBuilder
                ->insert(self::TABLE_SESSIONS)
                ->schema(['member', 'session'])
                ->values([':member', ':session'])
                ->bind('member', $account['id'])
                ->bind('session', $session)
                ->duplicate(['session'], true);

            $dbs->execute();

            // Update users profile active state
            $dbs->QueryBuilder
                ->update(self::TABLE_MEMBERS)
                ->set([
                    'active' => 1,
                    'lastonline' => ':date'
                ])
                ->bind('date', date(DATE_MYSQL))
                ->where('id = :id')
                ->bind('id', $account['id']);

            $dbs->execute();
            return true;
        }

        return $languages->custom(1014);
    }

    /**
     * Authorize a logout
     *
     * @return bool
     */
    public function authorizeLogout()
    {
        $this->setCookie('x', 1);
        return true;
    }

    /**
     * Authorize a new registration
     *
     * @param $email
     * @param $username
     * @param $password
     * @return mixed
     */
    public function authorizeRegistration($email, $username, $password)
    {
        $languages = $this->getModule('language');
        
        // does username exist?
        if ($this->checkUsernameExists(null, $username)) {
            return $languages->custom(1001);
        }

        // does email exist?
        if ($this->checkEmailExists(null, $email)) {
            return $languages->custom(1015);
        }

        return $this->createUser($email, $username, $password);
    }

    /**
     * Authorize a new forgot password request
     *
     * @param $login
     * @return bool
     */
    public function authorizeForgotPassword($login)
    {
        $languages = $this->getModule('language');
        $sanitize = $this->getModule('sanitize');

        $login = $sanitize->set($login)->validate('text')->strip()->sanitize()->length(3,128)->substring(128)->lowercase()->get();

        // if invalid login
        if (!$login) {
            return $languages->custom(1013);
        }

        // try get the user
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE_MEMBERS)
            ->where(['username = :p1', 'email = :p2'], 'OR')
            ->bind('p1', $login)
            ->bind('p2', $login)
            ->limit(0,1);

        $account = $dbs->get()->one();
        if (!$account) {
            return $languages->custom(1009);
        }

        // generate unique id
        $uniq = $this->generateSession();

        // update users unique id
        $dbs->QueryBuilder
            ->update(self::TABLE_MEMBERS)
            ->set([ 'uniq' => ':uniq' ])
            ->bind('uniq', $uniq)
            ->where('id = :id')
            ->bind('id', $account['id']);

        $dbs->execute();

        // send email
        $this->sendPasswordRecoveryEmail($account, $uniq);
        return true;
    }

    /**
     * Refresh a users token
     * @param $user
     */
    public function refreshSession($user = null)
    {
        $old = $this->getCookie(COOKIE_NAME);
        if (!$old || !$user) {
            return;
        }

        $dbs = $this->getModule('database');

        // generate session
        $session = $this->generateSession();
        $this->setCookie($session);

        $dbs->QueryBuilder
            ->update(self::TABLE_SESSIONS)
            ->set([ 'session' => ':session' ])->bind('session', $session)
            ->where('member = :member')->bind('member', $user->id)
            ->where('session = :session2')->bind('session2', $old);

        $dbs->execute();
        return true;
    }
}
