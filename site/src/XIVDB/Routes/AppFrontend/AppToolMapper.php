<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppToolMapper
//
trait AppToolMapper
{
    protected function _toolMapper()
    {
        //
        // Submit mapper data
        //
        $this->route('/mapper/submit', 'GET|POST', function(Request $request)
        {
            // get type
            if ($data = $request->get('data')) {
                // init mapper
                $mapper = $this->getModule('mapper');
                $mapper->setData($data)->process();

                return $this->json([
    				'status' => true,
    				'message' => sprintf('Saved %s pieces of data to XIVDB. Thank you!!', $mapper->count),
    			]);
            }

            return $this->json([
				'status' => false,
				'message' => 'Did not receive valid data',
			]);
        });
    }
}
