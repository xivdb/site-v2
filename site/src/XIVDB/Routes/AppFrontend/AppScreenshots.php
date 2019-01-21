<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppScreenshots
//
trait AppScreenshots
{
    protected function _screenshots()
    {
        //
        // Get screenshots
        //
        $this->route('/screenshots/get/{contentId}/{uniqueId}', 'GET|OPTIONS', function(Request $request, $contentId, $uniqueId)
        {
            $this->noApi($request);
            $screenshots = $this->getModule('screenshots');
            return $this->json($screenshots->get($uniqueId, $contentId));
        });

        //
        // Upload a screenshot
        //
        $this->route('/screenshots/upload', 'POST', function(Request $request)
        {
            $this->noApi($request);
            $screenshots = $this->getModule('screenshots');
            return $this->json($screenshots->upload($request));
        });

        //
        // Delete a screenshot
        //
        $this->route('/screenshots/delete', 'POST', function(Request $request)
        {
            $this->noApi($request);
            $screenshots = $this->getModule('screenshots');
            return $this->json($screenshots->delete($request));
        });
    }
}
