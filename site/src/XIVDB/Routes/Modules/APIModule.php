<?php

namespace XIVDB\Routes\Modules;

use Symfony\Component\HttpFoundation\Request;

//
// API module
//
trait APIModule
{
    /**
     * Is this an API request?
     *
     * @param Request $request
     * @return bool
     */
    public function isApiRequest(Request $request)
    {
        $host = $request->getHost();
        $host = explode('.', $host);

        // if API is the first path part
        if (trim($host[0]) == 'api') {
            $this->apiAnalytics($request);
            return true;
        }

        return false;
    }

    /**
     * Not allowed to do API requests on this route.
     *
     * @param Request $request
     * @return $this
     */
    public function noApi(Request $request)
    {
        // ensure it is an API request
        if ($this->isApiRequest($request)) {
            // display error
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            exit(json_encode([
                'status' => '200',
                'error' => 'Sorry! This content is not part of the API visit https://github.com/xivdb/api for more information. If you would like the API data for this page, please create an issue on Github.'
            ]));
        }

        return $this;
    }

    /**
     * @return array|string
     */
    public function getUrlType()
    {
        $type = $this->request->getPathInfo();
        $type = explode('/', $type);
        $type = array_filter($type);
        $type = array_values($type);
        $type = $type[0];
        $type = trim($type);
        $type = strtolower($type);

        return $type;
    }

    /**
     * @param Request $request
     */
    public function apiAnalytics(Request $request)
    {
        $data = [
            'an' => 'xivdb_api',
            'aid' => '001',
            'av' => '2.0',

            // Document referrer
            'dr' => $request->headers->get('referer'),

            // Document title
            'dt' => $request->getScriptName(),

            // Document path
            'dp' => $request->getPathInfo(),

            // Document location url
            'dl' => $request->getUri(),

            // Document host
            'dh' => $request->getSchemeAndHttpHost(),
        ];

        // google analytics
        $ga = $this->getModule('google-analytics');
        $ga->hit($data);
    }
}
