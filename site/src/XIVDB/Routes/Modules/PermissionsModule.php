<?php

namespace XIVDB\Routes\Modules;

//
// Permissions module
//
trait PermissionsModule
{
    /**
     * Must be logged in, if not redirect
     *
     * @param bool $isJson
     * @return $this
     */
    public function mustBeOnline($isJson = false)
    {
        if (!$this->getUser()) {
            if ($isJson) {
                header('Content-Type: application/json');
                header('Access-Control-Allow-Origin: *');
                die('Account not online');
            }

            header(sprintf('Location: %s/login', SECURE));
            exit();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function mustBeAdmin()
    {
        $this->mustBeOnline();

        if (!$this->getUser()->isAdmin()) {
            die('Admin only');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function mustBeModerator()
    {
        $this->mustBeOnline();

        if (!$this->getUser()->isModerator()) {
            die('Moderator/Admin Only');
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function mustBeDev()
    {
        if (!DEV) {
            die('Dev enviornment restriction enabled, permission denied.');
        }

        return $this;
    }
}
