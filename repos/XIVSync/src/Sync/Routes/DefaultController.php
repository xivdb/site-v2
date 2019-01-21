<?php

namespace Sync\Routes;

use Symfony\Component\HttpFoundation\Request;

trait DefaultController
{
    /**
     * Characters
     */
    protected function _default()
    {
        /**
         * Get a character
         */
        $this->route('/online', 'GET', function(Request $request)
        {
            return $this->json(1);
        });
    }
}
