<?php

namespace XIVDB\Apps\Site;

/**
 * A simple CSRF class to protect forms against CSRF attacks. The class uses
 * PHP sessions for storage.
 *
 * @author Raahul Seshadri
 *
 */
class CsrfManager
{
    /**
     * The namespace for the session variable and form inputs
     * @var string
     */
    private $namespace;

    /**
     * Initializes the session variable name, starts the session if not already so,
     * and initializes the token
     *
     * @param string $namespace
     */
    public function __construct($namespace = '_csrf')
    {
        $this->namespace = $namespace;

        if (session_id() === '') {
            session_start();
        }

        $this->setToken();
    }

    /**
     * Return the token from persistent storage
     *
     * @return string
     */
    public function getToken()
    {
        return $this->readTokenFromStorage();
    }

    /**
     * Similar to get token but refreshes it first.
     *
     * @return string
     */
    public function getNewToken()
    {
        return $this->refreshToken()->getToken();
    }

    /**
     * Verify if supplied token matches the stored token
     *
     * @param string $userToken
     * @return boolean
     */
    public function isTokenValid($userToken)
    {
        return ($userToken === $this->readTokenFromStorage());
    }

    /**
     * Very similar to token valid but kills the site if it fails
     *
     * @param $userToken
     */
    public function isRequestTokenValid($userToken)
    {
       if (!$this->isTokenValid($userToken)) {
           die;
       }

       return $this;
    }

    /**
     * Refresh a token
     */
    public function refreshToken()
    {
        $this->setToken(true);
        return $this;
    }

    /**
     * Generates a new token value and stores it in persisent storage, or else
     * does nothing if one already exists in persisent storage
     */
    private function setToken($force = false)
    {
        $storedToken = $this->readTokenFromStorage();

        if ($force || $storedToken === '') {
            $token = md5(uniqid(rand(), TRUE));
            $this->writeTokenToStorage($token);
        }
    }

    /**
     * Reads token from persistent sotrage
     * @return string
     */
    private function readTokenFromStorage()
    {
        if (isset($_SESSION[$this->namespace])) {
            return $_SESSION[$this->namespace];
        } else {
            return '';
        }
    }

    /**
     * Writes token to persistent storage
     */
    private function writeTokenToStorage($token)
    {
        $_SESSION[$this->namespace] = $token;
    }
}