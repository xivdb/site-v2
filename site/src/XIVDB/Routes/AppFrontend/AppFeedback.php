<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

use XIVDB\Apps\Site\Feedback;
use \Moment\Moment;

//
// AppFeedback
//
trait AppFeedback
{
    protected function _feedback()
    {
        //
        // View feedback
        //
        $this->route('/feedback', 'GET', function(Request $request)
        {
            $this->setLastUrl($request);
            $this->noApi($request);

            $filter = $request->get('folder');

            $feedback = new Feedback();
            $list = $feedback->all($filter);
            $folders = $feedback->folders();

            return $this->respond('Feedback/list.twig', [
                'feedback' => $list,
                'folders' => $folders,
            ]);
        });

        //
        // View a specific feedback
        //
        $this->route('/feedback/view/{id}', 'GET|POST', function(Request $request, $id)
        {
            $this->setLastUrl($request);
            $this->noApi($request);

            $feedback = new Feedback();
            $view = $feedback->get($id);
            $folders = $feedback->folders();

            if (!$view) {
                return $this->show404();
            }

            return $this->respond('Feedback/view.twig', [
                'feedback' => $view,
                'folders' => $folders,
				'data_misc' => $view['data_misc'] ? json_encode(json_decode($view['data_misc']), JSON_PRETTY_PRINT) : false,
            ]);
        });

        //
        // Delete a feedback post
        //
        $this->route('/feedback/delete/{id}', 'GET', function(Request $request, $id)
        {
            $this->noApi($request);
            $this->mustBeAdmin();

            $feedback = new Feedback;
            $feedback->delete($id);

            return $this->redirect('/feedback');
        });

        //
        // Place feedback into a folder
        //
        $this->route('/feedback/folder/{id}', 'GET|POST', function(Request $request, $id)
        {
            $this->noApi($request);
            $this->mustBeAdmin();

            $feedback = new Feedback;
            $feedback->tag($id, $request->get('folder'));

            return $this->redirect('/feedback/view/'. $id);
        });

        //
        // Submit some feedback
        //
        $this->route('/feedback/submit', 'POST', function(Request $request)
        {
            $this->noApi($request);
			$feedback = new Feedback();
            return $this->json($feedback->submit($request));
        });

        //
        // Respond to some feedback
        //
        $this->route('/feedback/reply', 'POST', function(Request $request)
        {
            $this->noApi($request);
			$feedback = new Feedback();
			return $this->json($feedback->reply($request));
        });
    }
}
