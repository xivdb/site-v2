<?php

namespace XIVDB\Routes\Modules;

use Symfony\Component\HttpFoundation\ResponseHeaderBag,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use XIVDB\Apps\Site\Session;

//
// Provide response functions
//
trait ResponseModule
{
    //
    // Respond with a twig template injected wiht some data
    //
    public function respond($template, $data = [])
    {
        // add alerts
        $session = new Session();
        $data['alerts'] = [
            'success' => $session->get('success'),
            'error' => $session->get('error'),
        ];

        // remove sessions
        $session->remove('success');
        $session->remove('error');

        // this is done here rather than in the "before" listener
        // as "before" is run before my code.
        $data['system'] = $this->getSystemInfo();

        // response
        return $this->get('twig')->render($template, $data);
    }

    //
    // Redirect to another url
    //
    public function redirect($url)
    {
        return $this->get()->redirect($url);
    }

    //
    // Respond with json
    //
    public function json($data, $status = 200)
    {
        $data = $this->utf8ize($data);

        //print_r(json_encode($data));
        //print_r(json_last_error());
        return $this->SilexApplication->json($data ? $data : [], $status, [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, X-Auth-Token, X-CSRF-Token',
            'Cache-Control' => 'max-age='. 3600,
            'Expires' => gmdate('r', time()+3600)
        ]);
    }

    /**
     * @param $message
     * @param $code
     * @return mixed
     */
    public function error($message, $code = 403)
    {
        $response = [
            'code' => $code,
            'error' => $message,
        ];

        return $this->json($response, $code);
    }

    /**
     * Fix utf8 issues
     * @param $mixed
     * @return array|string
     */
    public function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } else if (is_string ($mixed)) {
            return iconv( "UTF-8", "UTF-8", $mixed );
        }
        return $mixed;
    }

    //
    // Send a file to the user
    //
    public function send($file, $name = 'Download')
    {
        // Append XIVDB to the name
        $name = sprintf('XIVDB - %s', $name);

        // ensure file exists, else 404
        if (!file_exists($file)) {
            $this->get()->abort(404);
        }

        // if name, send a bit differently.
        if ($name) {
            return $this
                ->get()
                ->sendFile($file)
                ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);
        }

        return $this->get()->sendFile($file);
    }
}
