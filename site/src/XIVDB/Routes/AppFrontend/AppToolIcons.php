<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppTools
//
trait AppToolIcons
{
    protected function _icons()
    {
        //
        // Icons
        //
        $this->route('/icons', 'GET', function(Request $request)
        {
            $icons = $this->getModule('icons');
            $path = $request->get('path');

            return $this->respond('Tools/Icons/index.html.twig', [
                'path' => $path,
                'folders' => $icons->getFolders($path),
                'icons' => $icons->getIcons($path),
            ]);
        });
    }
}
