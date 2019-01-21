<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppComments
//
trait AppComments
{
    protected function _comments()
    {
        //
        // Get comments for a content piece
        //
        $this->route('/comments/get/{contentId}/{uniqueId}', 'GET|OPTIONS', function(Request $request, $contentId, $uniqueId)
        {
            $this->noApi($request);

            $comments = $this->getModule('comments');
            $data = $comments->get($uniqueId, $contentId);

            // is online
            foreach($data as $i => $comment) {
                // set state
                $data[$i]['is_online'] = false;
                $data[$i]['deleted'] = $data[$i]['deleted'] ? true : null;

                // get user
                if ($this->getUser()) {
                    $data[$i]['is_online'] = true;
                    $data[$i]['is_author'] = ($this->getUser()->id == $comment['member']);
                }
            }

            return $this->json($data);
        });

        //
        // Post a new comment
        //
        $this->route('/comments/post', 'POST', function(Request $request)
        {
            $this->noApi($request);
            $comments = $this->getModule('comments');
            return $this->json($comments->post($request));
        });

        //
        // Update a comment
        //
        $this->route('/comments/update', 'POST', function(Request $request)
        {
            $this->noApi($request);
            $comments = $this->getModule('comments');
            return $this->json($comments->update($request));
        });

        //
        // Delete a comment
        //
        $this->route('/comments/delete', 'POST', function(Request $request)
        {
            $this->noApi($request);
            $comments = $this->getModule('comments');
            return $this->json($comments->delete($request));
        });
    }
}
