<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppCommunity
// - Comments page
// - Screenshots page
//
trait AppCommunity
{
    //
    // Comment stuff
    //
    protected function _community_comments()
    {
        //
        // Page listing stuff on comments
        //
        $this->route('/community/comments', 'GET', function(Request $request)
        {
            $this->noApi($request);

			$comments = $this->getModule('comments');

			return $this->respond('Pages/Community/comments.html.twig',  [
                'recent' => $comments->getRecent(250),
				'top' => $comments->getTopOverall(30),
                'total' => $comments->getTotalSinceDate(),
			]);
        });
    }

    //
    // Screenshots stuff
    //
    protected function _community_screenshots()
    {
        //
        // Page listing stuff on comments
        //
        $this->route('/community/screenshots', 'GET', function(Request $request)
        {
            $this->noApi($request);

			$screenshots = $this->getModule('screenshots');

			return $this->respond('Pages/Community/screenshots.html.twig',  [
                'recent' => $screenshots->getRecent(250),
                'total' => $screenshots->getTotalSinceDate(),
			]);
        });
    }
}
